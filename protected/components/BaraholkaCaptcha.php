<?

class BaraholkaCaptcha
{
/**
* The name of the GET parameter indicating whether the CAPTCHA image should be regenerated.
*/
const REFRESH_GET_VAR='refresh';
/**
* Prefix to the session variable name used by the action.
*/
const SESSION_VAR_PREFIX='Yii.CCaptchaAction.';
/**
* @var integer how many times should the same CAPTCHA be displayed. Defaults to 3.
*/
public $testLimit=1;
/**
* @var integer the width of the generated CAPTCHA image. Defaults to 120.
*/
public $width=200;
/**
* @var integer the height of the generated CAPTCHA image. Defaults to 50.
*/
public $height=75;
/**
* @var integer padding around the text. Defaults to 2.
*/
public $padding=0;
/**
* @var integer the background color. For example, 0x55FF00.
* Defaults to 0xFFFFFF, meaning white color.
*/
public $backColor=0xFFFFFF;
/**
* @var integer the font color. For example, 0x55FF00. Defaults to 0x2040A0 (blue color).
*/
public $foreColor=0x2040A0;
/**
* @var integer the minimum length for randomly generated word. Defaults to 6.
*/
public $minLength=6;
/**
* @var integer the maximum length for randomly generated word. Defaults to 7.
*/
public $maxLength=7;
/**
* @var string the TrueType font file. Defaults to Duality.ttf which is provided
* with the Yii release.
*/
public $fontFile;

public $code;

/**
* Renders the CAPTCHA image based on the code.
* @param string the verification code
* @return string image content
*/
function renderImage()
{
    $this->code = $code = $this->generateVerifyCode();

    $image=imagecreatetruecolor($this->width,$this->height);
    $backColor=imagecolorallocate($image,
    (int)($this->backColor%0x1000000/0x10000),
    (int)($this->backColor%0x10000/0x100),
    $this->backColor%0x100);
    imagefilledrectangle($image,0,0,$this->width,$this->height,$backColor);
    imagecolordeallocate($image,$backColor);

    $foreColor=imagecolorallocate($image,
    (int)($this->foreColor%0x1000000/0x10000),
    (int)($this->foreColor%0x10000/0x100),
    $this->foreColor%0x100);

    if($this->fontFile===null)
    $this->fontFile='./fonts/SpicyRice.ttf';

    $offset=2;
    $length=strlen($code);
    $box=imagettfbbox(30,0,$this->fontFile,$code);
    $w=$box[4]-$box[0]-$offset*($length-1);
    $h=$box[1]-$box[5];
    $scale=min(($this->width-$this->padding*2)/$w,($this->height-$this->padding*2)/$h);
    $x=10;
    $y=round($this->height*27/40);
    for($i=0;$i<$length;++$i)
    {
    $fontSize=(int)(rand(26,32)*$scale*0.8);
    $angle=rand(-10,10);
    $letter=$code[$i];
    $box=imagettftext($image,$fontSize,$angle,$x,$y,$foreColor,$this->fontFile,$letter);
    $x=$box[2]-$offset;
    }

    imagecolordeallocate($image,$foreColor);

    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Content-Transfer-Encoding: binary');
    header("Content-type: image/png");
    imagepng($image);
    imagedestroy($image);
}


function generateVerifyCode()
{
    if($this->minLength<3)
        $this->minLength=3;
    if($this->maxLength>20)
        $this->maxLength=20;
    if($this->minLength>$this->maxLength)
        $this->maxLength=$this->minLength;
    $length=rand($this->minLength,$this->maxLength);

    $letters='bcdfghjklmnpqrstvwxyz';
    $vowels='aeiou';
    $code='';
    for($i=0;$i<$length;++$i)
    {
        if($i%2 && rand(0,10)>2 || !($i%2) && rand(0,10)>9)
            $code.=$vowels[rand(0,4)];
        else
            $code.=$letters[rand(0,20)];
    }

    return $code;
}



}
