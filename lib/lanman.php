<?php

/**
 * Language Manager
 *
 * @package    App\Core
 * @category   Lib
 * @author     Milad Abooali <m.abooali@hotmail.com>
 * @copyright  2012 - 2020 Codebox
 * @license    http://codebox.ir/license/1_0.txt  Codebox License 1.0
 * @version    3.0.0
 */
class LangMan
{
    /**
     * @var string $path   Language file path
     * @var string $language    selected language
     * @var bool $devMod    if true show raw phrase on not found
     */
    private
        $path;
    public
        $language='english',
        $devMod;

    static public $PHRASE_LIST=array();
    /**
     * LangMan Constructor.
     * @param array $database Database Connection Information
     */
    function __construct(string $language,bool $devMod=false)
    {
        $this->devMod = $devMod;
        $this->set($language);
    }

    /**
     * LangMan Destructor.
     */
    function __destruct()
    {
        self::$PHRASE_LIST = array();
    }

    /**
     * @param string $language selected language file
     * @return bool
     */
    public function set(string $language) {
        $this->language = $language;
        $file_path = $_SERVER['DOCUMENT_ROOT'].'/languages/'.$language.'.ini';
        $this->path = (file_exists($file_path)) ? $file_path : $_SERVER['DOCUMENT_ROOT'].'/languages/english.ini';
        self::$PHRASE_LIST = array();
        $_SESSION['language'] = $language;
        self::$PHRASE_LIST = parse_ini_file($this->path,true);
        return (self::$PHRASE_LIST) ?? false;
    }

    /**
     * Language
     * @param string $phrase language text
     * @param array $vars vars in array to insert in text
     * @param string $section The section of language file
     * @param bool $eol if end of line
     */
    public function T(string $phrase, string $section = 'core', $vars = [], bool $eol = false)
    {
        $output='';
        if (self::$PHRASE_LIST[$section][$phrase] ?? false) {
            $output.= ($vars) ? vsprintf(self::$PHRASE_LIST[$section][$phrase], $vars) : self::$PHRASE_LIST[$section][$phrase];
            if ($eol) $output.= PHP_EOL;
        } else if ($this->devMod) {
            $output.= ('['.$section.']_'.'['.$phrase.']');
            if ($eol) $output.= PHP_EOL;
        } else {
            $output.= ('['.$phrase.']');
            if ($eol) $output.= PHP_EOL;
        }
        return $output;
    }

    /**
     * Get Word In Lang
     * @param string $phrase language text
     * @param array|string|null $vars vars in array to insert in text
     * @param string $section The section of language file
     * @param bool $eol if end of line
     */
    public function getW(string $lang, string $phrase,string $section='core', $vars=null, bool $eol=false) {
        $output='';
        if (self::$PHRASE_LIST[$section][$phrase] ?? false) {
            $output.= ($vars) ? vsprintf(self::$PHRASE_LIST[$section][$phrase], $vars) : self::$PHRASE_LIST[$section][$phrase];
            if ($eol) $output.= PHP_EOL;
        } else if ($this->devMod) {
            $output.= ('['.$section.']_'.'['.$phrase.']');
            if ($eol) $output.= PHP_EOL;
        } else {
            $output.= ('['.$phrase.']');
            if ($eol) $output.= PHP_EOL;
        }
        return $output;
    }

    /**
     * Get Language File
     * @param string $language target language
     * @return array|bool   return array of phrases or false
     */
    public function get(string $language) {
        $file_path = $_SERVER['DOCUMENT_ROOT'].'/languages/'.$language.'.ini';
        $instance = (file_exists($file_path)) ? $file_path : $_SERVER['DOCUMENT_ROOT'].'/languages/english.ini';
        $instance_array = parse_ini_file($instance,true);
        return ($instance_array) ?? false;
    }

    /**
     * Get Language File
     * @param string $language target language
     * @param array $phrases translate of phrases
     * @return array|bool   return array of phrases or false
     */
    public function update(string $language,array $phrases) {
        $file_path = $_SERVER['DOCUMENT_ROOT'].'/languages/'.$language.'.ini';
        $ini_output = PHP_EOL.';==== Updated on '.date("Y-m-d h:i:sa").PHP_EOL;
        foreach ($phrases as $section => $phrase) {
            $ini_output .= "[$section]".PHP_EOL;
            if ($phrase) foreach ($phrase as $k => $v) $ini_output .= "$k=$v" . PHP_EOL;
        }
        $file = fopen($file_path, "w");
        fwrite($file,$ini_output);
        return (fclose($file));
    }


}

### Test Pad
/*
    $_L = new LangMan('fa',true);

    // Test update
    $phrases['user'] = array(
        'user' => 'کاربر'
    );
    $phrases['core'] = array(
        'hello' => 'سلام',
        'bye' => 'خدانگهدار %s'
    );
    $_L->update('fa',$phrases);

    //Test translate
    $_L->T('bye', array('ali',50),'core',1);
    $_L->T('bye', 'ali','user');

*/