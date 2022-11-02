<?php

/**
 * Blocks
 * Public Static Functions
 */
class blocks{

    /**
     * Permit Error
     * @param string $target
     * @param string $action
     * @param string $role
     * @return string
     */
    public static function permitError(string $target, string $action, string $role): string
    {
        $block =
        '
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <div>
               <p>
                   <i class="fas fa-exclamation-triangle me-2"></i>
                   You are not permitted to:
                </p> 
                <hr>
                <p>"<strong>'.$action.'</strong>" on "<strong>'.$target.'</strong>" as a "<strong>'.$role.'</strong>"</p>
            </div>
        </div>
        ';
        return eFun::htmlMinify($block);
    }

    /**
     * Screen
     * @param $screen
     * @return array|string|string[]
     */
    public static function screen($screen, $params=null){
        ob_start();
        try{
            If(is_file(APP_PATH."/screens/$screen.php"))
                include_once(APP_PATH."/screens/$screen.php");
            else
                throw new Exception("Screen <strong>$screen</strong> not found!");
            $block = ob_get_contents();
        } catch (Exception $e){
            $block = '<div id="'.$screen.'" class="screen-wrapper alert alert-warning">'.$e->getMessage()."</div>";
        }
        ob_end_clean();
        return eFun::htmlMinify($block);
    }

    /**
     * Form
     * @param $form
     * @return array|string|string[]
     */
    public static function form($form, $params=null){
        ob_start();
        try{
            If(is_file(APP_PATH."/forms/$form.php"))
                include_once(APP_PATH."/forms/$form.php");
            else
                throw new Exception("Forms <strong>$form</strong> not found!");
            $block = ob_get_contents();
        } catch (Exception $e){
            $block = '<div id="'.$form.'" class="screen-wrapper alert alert-warning">'.$e->getMessage()."</div>";
        }
        ob_end_clean();
        return eFun::htmlMinify($block);
    }

    /**
     * Wizard
     * @param $wizard
     * @return array|string|string[]
     */
    public static function wizard($wizard){
        ob_start();
        try{
            If(is_file(APP_PATH."/wizards/$wizard.php"))
                include_once(APP_PATH."/wizards/$wizard.php");
            else
                throw new Exception("Wizard <strong>$wizard</strong> not found!");
            $block = ob_get_contents();
        } catch (Exception $e){
            $block = '<div id="'.$wizard.'" class="screen-wrapper alert alert-warning">'.$e->getMessage()."</div>";
        }
        ob_end_clean();
        return eFun::htmlMinify($block);
    }

    /**
     * Widget
     * @param $form
     * @return array|string|string[]
     */
    public static function widget($widget){
        ob_start();
        try{
            If(is_file(APP_PATH."/widgets/$widget.php"))
                include_once(APP_PATH."/widgets/$widget.php");
            else
                throw new Exception("Widget <strong>$widget</strong> not found!");
            $block = ob_get_contents();
        } catch (Exception $e){
            $block = '<div id="'.$widget.'" class="screen-wrapper alert alert-warning">'.$e->getMessage()."</div>";
        }
        ob_end_clean();
        return eFun::htmlMinify($block);
    }

}