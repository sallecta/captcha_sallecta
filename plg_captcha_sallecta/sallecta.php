<?php

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\HTML\HTMLHelper;
/**
 *
 */
class PlgCaptchasallecta extends CMSPlugin
{
	private $info = array();
	
	
	public $containerid;
	public $objectid;
	public $prefix;
	
	public function __construct($subject, $config) {
		
		parent::__construct($subject, $config);
		
		$this->containerid = md5('captchasallecta_container' . time());
		$this->objectid = md5('captchasallecta_object' . time());
		$this->prefix = $this->params->get('prefix', 'hx');
		$this->info["dir_web_0"] = "/plugins/captcha/sallecta";
		$this->info["dir_srv_0"] = JPATH_ROOT . $this->info["dir_web_0"];
		$this->info["firstlog"] = 1;
		$this->info["path_img_loading"] = $this->info["dir_web_0"] . "/images/loading(icons8.com).gif";
	}
	
	
	private function log($arg_msg) 
	{
		$file_path = $this->info["dir_srv_0"].'/log.html';
		$msg = "<p>" . microtime(true) . ": " . $arg_msg . "</p>\n";
		if ($this->info['firstlog'] == 1)
		{
			file_put_contents($file_path, $msg, LOCK_EX);
			$this->info['firstlog'] = 0;
		}
		else
		{
			file_put_contents($file_path, $msg, FILE_APPEND | LOCK_EX);
		}
	}	
	
	/**
	 * Load the language file on instantiation.
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	/**
	 *
	 *
	 * @param   string  $name   The name of the field.
	 * @param   string  $id     The id of the field.
	 * @param   string  $class  The class of the field. This should be passed as
	 *                          e.g. 'class="required"'.
	 * @return  string  The HTML to be embedded in the form.
	 * @since  2.5
	 */
	public function onDisplay($name = 'captchasallecta', $id = 'captchasallecta', $class = 'required')
	{ 
		
		HTMLHelper::_('jquery.framework');
		
		$image_width  = $this->params->get('image_width', 400);
		$image_height = $image_width/4;
		$submitTimeoutValue = $this->params->get('submit_timeout_value', '3');
		
		// Description 
		switch ($this->params->get('captcha_type', '1')) {
			case '1':
				$captcha_describtion = Text::_('PLG_CAPTCHA_SALLECTA_LETTERS_DESCRIPTION');
				break;
			case '2':
				$captcha_describtion = Text::_('PLG_CAPTCHA_SALLECTA_ARITHMETIC_DESCRIPTION');
				break;
			case '3':
				$captcha_describtion = Text::_('PLG_CAPTCHA_SALLECTA_LEXICAL_DESCRIPTION');
				break;
		}
		$captcha_loading = Text::_('PLG_CAPTCHA_SALLECTA_LOADING');
		
		// Trap field
		if ($this->params->get('trap_field', '1'))
		{
			$trapField = '<input type="text" name="captchasallecta_trap_field" style="display:none !important;" />';
		}
		else
		{
			$trapField = '';
		}
		
		// Timeout before submit
		if ($this->params->get('submit_timeout', '1'))
		{
			$input = '';
			$loadProgress = '<div class="'.$this->prefix.'_captchasallecta_progress_body"><div class="'.$this->prefix.'_captchasallecta_progress_line" style="width: 0%"></div></div>';
			$background_url = $this->info["path_img_loading"];
		}
		else
		{ // never runs
			$input = self::client_create_captcha_field();
			$loadProgress = '';
			$background_url = self::client_create_captcha_image();
		}
		
		$html_container = 
				'<div id="'.$this->containerid.'" class="'.$this->prefix.'_captchasallecta_container">'.
				'<div id="'.$this->objectid.'" class="'.$this->prefix.'_captchasallecta_object">'.$loadProgress.'</div>'.
				'<div style="clear:both"></div>'.
				'<div id="captchasallecta_field-lbl" class="captchasallecta_label hasPopover" for="captchasallecta_field" data-original-title="Captcha" data-content="'.$captcha_loading.'">'.$captcha_loading.'</div>'.
				'<div>'.$trapField.$input.'</div>'.
				'</div>';
				
		// Style
		Factory::getDocument()->addStyleDeclaration("
			.".$this->prefix."_captchasallecta_container {
				margin: 8px 0;
			}
			.".$this->prefix."_captchasallecta_object {
				position: relative;
				width: ".$image_width."px;
				height: ".$image_height."px;
				margin: 8px 0;
				background-image: url('".$background_url."');
				background-repeat: no-repeat;background-position: center;
			}
			.".$this->prefix."_captchasallecta_progress_body
			{
				position: absolute;
				bottom: 0;
				right: 0;
				width: 100%;
				height: 2px;
				border: 1px solid #eee;
				border-radius: 0px;
			}
			.".$this->prefix."_captchasallecta_progress_line
			{
				height: 2px;
				border-radius: 0px;
				background: linear-gradient(230deg, #51a790, #00ffbd, #47907d, #00ffbd);
				background-size: 800% 800%;
				-webkit-animation: gradientAnimation 3s ease infinite;
				-moz-animation: gradientAnimation 3s ease infinite;
				animation: gradientAnimation 3s ease infinite;
				transition: width ".($submitTimeoutValue*1000)."ms ease-in-out;
			}
			.captchasallecta_label {
				display: inline-block;
			}
		");
		
		JFactory::getDocument()->addScriptOptions('captchasallecta', array
			(
				'prefix' => $this->prefix,
				'submitTimeoutValue' => $submitTimeoutValue,
				'containerid' => $this->containerid,
				'objectid' => $this->objectid,
				'msg_error_captcha_reload' => Text::_('PLG_CAPTCHA_SALLECTA_RELOAD_CAPTCHA_ERROR'),
				'sessionFormToken' => Session::getFormToken(),
				'submitTimeout' => $this->params->get('submit_timeout', '1'),
				'msg_success' => $captcha_describtion,
				'msg_loading' => $captcha_loading
			));
		
		Factory::getDocument()->addScript($this->info["dir_web_0"] . "/client/scripts/captchasallecta.js");
		
		return $html_container;
	} // public function onDisplay
	
	/*
	* Field
	*/
	public function client_create_captcha_field()
	{
		$form = new Form('captchasallecta');
		$form->load( new \SimpleXMLElement('<form name="captchasallecta"><fieldset addfieldpath="'.$this->info["dir_srv_0"].'/fields/"><field name="captchasallecta_field" type="captchasallecta" label="Captcha" required="true" validate="captchasallecta" class="validate-captchasallecta" size="15" autocomplete="off" filter="html"/></fieldset></form>'));
		$input = '<div style="display:none !important;">' .$form->getLabel('captchasallecta_field') . '</div>' . $form->getInput('captchasallecta_field') . '<span id="captchasallecta_reload" class="btn '.$this->prefix.'_captchasallecta_reload" title="' . Text::_('PLG_CAPTCHA_SALLECTA_RELOAD') . '">' . Text::_('PLG_CAPTCHA_SALLECTA_RELOAD') . '</span>';
		
		return $input;
	}
	
	private function client_create_captcha_image()
	{
		$session = Factory::getSession();
		$oldImages = glob($this->info["dir_srv_0"].'/imagex/*.png');
		if($oldImages){
			foreach($oldImages as $oldImage){
				if(file_exists($oldImage)){
					unlink($oldImage);
				}
			}
		}
		
		// Unable to initialize GD library
		$image = imagecreatetruecolor(400, 100) or die(Text::_('PLG_CAPTCHA_SALLECTA_GD_ERROR')); 

		$captcha_image_name = base64_encode($session->getId() . time()). '.png';
		
		// Switch captcha type
		switch ($this->params->get('captcha_type', '1')) {
			case '1':
				$image = self::typeLetters($image, 400, 100);
				break;
			case '2':
				$image = self::typeArithmetic($image, 400, 100);
				break;
			case '3':
				$image = self::typeLexical($image, 400, 100);
				break;
		}
		
		//Horizontal Line
		if ($this->params->get('horizontal_line', '0')) {
			self::apply_horizontal_line($image, 400, 100);
		}
		
		// Wave
		if ($this->params->get('wave', '0')) {
			self::apply_wave($image, 400, 100);
		}
		
		//Random dots
		if ($this->params->get('random_dots', '0')) {
			self::apply_random_dots($image, 400, 100);
		}
		
		//Random Line
		if ($this->params->get('random_line', '0')) {
			self::apply_random_line($image, 400, 100);
		}
		
		if(!file_exists($this->info["dir_srv_0"]."/imagex/")){
			mkdir($this->info["dir_srv_0"] . "/imagex/");
		}
		
		imagepng($image, $this->info["dir_srv_0"] . '/imagex/' . $captcha_image_name);
		return $this->info["dir_web_0"] . '/imagex/' . $captcha_image_name;
		
	}
	
	/*
	* Letters captcha type
	*/
	private function typeLetters($image, $width, $height)
	{
		$session     = Factory::getSession();
		$numberValue = $this->params->get('letters_value', '6');
		$letters     = $this->params->get('letters', 'ABCEFGHIJKLMNPRSTVWXZ123456789');
		$len         = strlen($letters);
		
		$bg_color    = self::getColor($image, $this->params->get('background_color', 'white'));
		$text_color  = self::getColor($image, $this->params->get('text_color', 'black'));
		imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);
		
		$word = "";
		$font = imageloadfont($this->info["dir_srv_0"].'/fonts/font.gdf');
		$fh = imagefontheight($font);
		$fw = imagefontwidth($font);
			
		for ($i = 0; $i < $numberValue; $i++) {
			$letter = $letters[rand(0, $len - 1)];
			imagechar($image, $font, 25 + ($i * (360/$numberValue)), rand(0, 150 - $fh), $letter, $text_color);
			$word .= $letter;
		}
		
		$session->set('captchasallecta_word', md5($word));
		
		return $image;
	}
	
	/*
	* Arithmetic captcha type
	*/
	private function typeArithmetic($image, $width, $height)
	{
		$session    = Factory::getSession();
		$bg_color   = self::getColor($image, $this->params->get('background_color', 'white'));
		$text_color = self::getColor($image, $this->params->get('text_color', 'black'));
		
		$countIntA  = $this->params->get('arithmetic_value_a', 9);
		$countIntB  = $this->params->get('arithmetic_value_b', 9);
		$intA       = random_int(0, $countIntA);
		$intB       = random_int(0, $countIntB);
		
		$lenA       = strlen($intA);
		$lenB       = strlen($intB);
		$lenStr     = strlen($intA.$intB)+1;
		
		$word       = $intA+$intB;
		$session->set('captchasallecta_word', md5($word));
		
		$intA       = (string)$intA;
		$intB       = (string)$intB;
		
		imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);
		
		$font = imageloadfont($this->info["dir_srv_0"].'/fonts/font.gdf');
		$fh = imagefontheight($font);
		$fw = imagefontwidth($font);
		
		// First int
		for ($i = 0; $i < $lenA; $i++) {
			$var = $intA[$i];
			imagechar($image, $font, 25 + ($i * (360/$lenStr)), rand(0, 150 - $fh), $var, $text_color);
		}
		// +
		imagechar($image, $font, 25 + (($lenA) * (360/$lenStr)), rand(0, 150 - $fh), '+', $text_color);
		// Second int
		for ($i = 0; $i < $lenB; $i++) {
			$var = $intB[$i];
			imagechar($image, $font, 25 + ((1 + $lenA + $i) * (360/$lenStr)), rand(0, 150 - $fh), $var, $text_color);
		}
		
		return $image;
	}
	
	/*
	* Lexical captcha type
	*/
	private function typeLexical($image, $width, $height)
	{
		$session    = Factory::getSession();
		$bg_color   = self::getColor($image, $this->params->get('background_color', 'white'));
		$text_color = self::getColor($image, $this->params->get('text_color', 'black'));
		
		$default = 'fox,dog,cat,sea,sky,low,son,sun,wet,red,can,car,bed,bag,air,sit,big,eye,hot,fly,try,man,may,day,toy,one,two,six,ten,lie,pen,paw,owl,oil';
		$arrayWord  = explode(',', chop($this->params->get('lexical_words', $default), ','));
		$arrayIndex = trim(array_rand($arrayWord, 1));
		
		$word = $arrayWord[$arrayIndex];
		$session->set('captchasallecta_word', md5($word));
		
		$lenght = strlen($word);
		
		imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);
		
		$font = imageloadfont($this->info["dir_srv_0"].'/fonts/font.gdf');
		$fh = imagefontheight($font);
		$fw = imagefontwidth($font);
		
		for ($i = 0; $i < $lenght; $i++) {
			$var = $word[$i];
			imagechar($image, $font, 25 + ($i * (360/$lenght)), rand(0, 150 - $fh), $var, $text_color);
		}
		return $image;
	}
	
	/*
	* Get color
	*/
	private function getColor($image, $color)
	{
		switch ($color){
			case 'white':
				$rgb = imagecolorallocate($image, 255, 255, 255);
				break;
			case 'black':
				$rgb = imagecolorallocate($image, 0, 0, 0);
				break;
			case 'red':
				$rgb = imagecolorallocate($image, 255, 0, 0);
				break;
			case 'green':
				$rgb = imagecolorallocate($image, 0, 170, 0);
				break;
			case 'blue':
				$rgb = imagecolorallocate($image, 0, 0, 255);
				break;
			case 'orange':
				$rgb = imagecolorallocate($image, 255, 87, 34);
				break;
			case 'gray':
				$rgb = imagecolorallocate($image, 158, 158, 158);
				break;
			case 'brown':
				$rgb = imagecolorallocate($image, 121, 85, 72);
				break;
			case 'purple':
				$rgb = imagecolorallocate($image, 166, 16, 240);
				break;
			case 'yellow':
				$rgb = imagecolorallocate($image, 255, 255, 0);
				break;
		}
		return $rgb;
	}
	
	/*
	* Wave
	*/
	private function apply_wave($image, $width, $height)
	{		
		$x_period = 10;
		$y_period = 10;
		$y_amplitude = 5;
		$x_amplitude = 5;
		
		$xp = $x_period*rand(1,3);
		$k = rand(0,100);
		for ($a = 0; $a<$width; $a++)
			imagecopy($image, $image, $a-1, sin($k+$a/$xp)*$x_amplitude, 
				$a, 0, 1, $height);
			
		$yp = $y_period*rand(1,2);
		$k = rand(0,100);
		for ($a = 0; $a<$height; $a++)
			imagecopy($image, $image, sin($k+$a/$yp)*$y_amplitude, 
				$a-1, 0, $a, $width, 1);
		return $image;
	}
	
	/*
	* Horizontal line
	*/
	private function apply_horizontal_line($image, $width, $height)
	{		
		$horizontal_line_value     = $this->params->get('horizontal_line_value', '4');
		$horizontal_line_thickness = $this->params->get('horizontal_line_thickness', '1');
		$horizontal_color          = self::getColor($image, $this->params->get('horizontal_line_color', 'black'));
		imagesetthickness($image, $horizontal_line_thickness);
		
		$paddingIterr = 0;
		
		for ($i = 0; $i < $horizontal_line_value; $i++) {
			$padding = 100/($horizontal_line_value+1);
			$paddingIterr = $paddingIterr+$padding;
			imageline( $image, 0, $paddingIterr, $width, $paddingIterr, $horizontal_color );
		}
		return $image;
	}
	
	/*
	* Random dots
	*/
	private function apply_random_dots($image, $width, $height)
	{	
		$dots_value = $this->params->get('random_dots_value', '3000');
		$dot_color  = self::getColor($image, $this->params->get('random_dots_color', 'black'));
		for ($i = 0; $i < $dots_value; $i++) {
			imagesetpixel($image, rand() % $width, rand() % $height, $dot_color);
		}
		return $image;
	}
	
	/*
	* Random line
	*/
	private function apply_random_line($image, $width, $height)
	{
		$line_thickness = $this->params->get('random_line_thickness', '1');
		$line_value     = $this->params->get('random_line_value', '30');
		$line_color     = self::getColor($image, $this->params->get('random_line_color', 'black'));
		for ($i = 0; $i < $line_value; $i++) {
			imagesetthickness($image, $line_thickness);
			imageline( $image, rand() % $width, rand() % $height, rand() % $width, rand() % $height, $line_color );
		}
		return $image;
	}

	/**
	 * Calls an HTTP POST function to verify if the user's guess was correct
	 * @param   string  $code  Answer provided by user.
	 * @return  True if the answer is correct, false otherwise
	 * @since  2.5
	 */
	public function onCheckAnswer($code = NULL)
	{
		$app     = Factory::getApplication();
		$session = Factory::getSession();
		
		// Trap field
		if ($this->params->get('trap_field', '1'))
		{
			$trap = $app->input->get('captchasallecta_trap_field', '', 'STRING');
			if ($trap)
			{
				$this->_subject->setError(Text::_('PLG_CAPTCHA_SALLECTA_ERROR'));
				return false;
			}
		}
		
		$response  = $code ? $code : $app->input->get('captchasallecta_field', '', 'STRING');
		
		// Captcha empty
		if (!isset($response) || empty($response) || strlen($response) == 0)
		{
			$this->_subject->setError(Text::_('PLG_CAPTCHA_SALLECTA_IS_EMPTY'));
			$app->enqueueMessage(Text::_('PLG_CAPTCHA_SALLECTA_IS_EMPTY'), 'error');
			return false;
		}
		
		// Captcha verification
		if (md5($response) != $session->get('captchasallecta_word'))
		{
			// Switch captcha type and set message
			switch ($this->params->get('captcha_type', '1')) {
				case '1':
					$app->enqueueMessage(Text::_('PLG_CAPTCHA_SALLECTA_LETTERS_ERROR'), 'error');
					break;
				case '2':
					$app->enqueueMessage(Text::_('PLG_CAPTCHA_SALLECTA_ARITHMETIC_ERROR'), 'error');
					break;
				case '3':
					$app->enqueueMessage(Text::_('PLG_CAPTCHA_SALLECTA_LEXICAL_ERROR'), 'error');
					break;
			}
			
			$this->_subject->setError(Text::_('PLG_CAPTCHA_SALLECTA_ERROR'));
			return false;
		}
		
		return true;
	}
	
	/*
	* Ajax functions
	*/
	public function onAjaxSallecta()
    {
		Session::checkToken('get') or die( 'Invalid Token' );
		$app     = Factory::getApplication();
		$action  = $app->input->get('action', '', 'STRING');
		if ($action)
		{
			switch ($action) {
				case 'reload':
					$data = array(self::client_create_captcha_image());
					break;
				case 'load':
					$data = json_encode(self::client_create_captcha_field());
					break;
				default:
					$data = '';
			}
		}
        return $data;
    }
	
}
