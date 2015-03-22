<?php
/**
 * @version   $Id: roksocialbuttons.php 11496 2013-06-15 04:49:11Z steph $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 */
class plgContentRokSocialButtons extends JPlugin
{
    /**
     * @param $context
     * @param $row
     * @param $params
     * @param $limitstart
     */
    public function onContentAfterTitle($context, &$row, &$params, $page=0)
    {
        if (JFactory::getApplication()->isAdmin()) return true;

        // Don't run this plugin when the content is being indexed
        if ($context == 'com_finder.indexer') return true;

        $jversion = new JVersion();
        if (version_compare($jversion->getShortVersion(), '2.5.10', '<')) {
            $this->_processContent($row);
        }
    }

    /**
     * @param $context
     * @param $row
     * @param $params
     * @param $limitstart
     */
    public function onContentPrepare($context, &$row, &$params, $page=0)
    {
        if (JFactory::getApplication()->isAdmin()) return true;

        // Don't run this plugin when the content is being indexed
        if ($context == 'com_finder.indexer') return true;

        $jversion = new JVersion();
        if (version_compare($jversion->getShortVersion(), '2.5.10', '>=')) {
            $this->_processContent($row);
        }
    }

    protected function _processContent(&$row){

        $add_method = $this->params->get('add_method', 2);
        $category_id = $this->params->get('catid', array());
        $display_position = $this->params->get('display_position', 0);

        // get the context of this row
        if (!isset($row->text) && isset($row->introtext)) {
            $text = $row->introtext;
        } else {
            $text = $row->text;
        }

        //if match method is on
        if((int)$add_method == 1 || (int)$add_method == 2){

            // simple performance check to determine whether bot should process further
            if (JString::strpos($text, 'socialbuttons') !== false){

                $matches = array();
                $regex = '/\{socialbuttons(.*)\}/i';
                preg_match_all($regex, $text, $matches, PREG_SET_ORDER);

                if(sizeof($matches) > 0){

                    $this->_addHead($row);

                    foreach ($matches as $match) {

                        $passed_param = $match[1];
                        $module_params = array();

                        if (isset($passed_param)) {
                            $param_match = array();
                            preg_match_all('/((\w+)\="(.*)")/i', $passed_param, $param_match, PREG_SET_ORDER);
                            foreach ($param_match as $pmatch) {
                                $module_params[$pmatch[2]] = $pmatch[3];
                            }
                        }

                       $text = preg_replace($regex, $this->_getCode($row), $text, 1);
                    }
                }
            }
        }

        //if category method is on
        if(((int)$add_method == 0 || (int)$add_method == 2) && isset($row->catid)){

            if(in_array("",$category_id) || in_array($row->catid,$category_id)){
                $this->_addHead($row);
                if((int)$display_position){
                    $text = $text.$this->_getCode($row);
                } else {
                    $text = $this->_getCode($row).$text;
                }
            }
        }

        $this->_setContentText($row, $text);
    }

    /**
     *
     */
    protected function _addHead(){

        $document = JFactory::getDocument();
        $template = JFactory::getApplication()->getTemplate();

        if (!defined("ROKSOCIALBUTTONS")) {
            define("ROKSOCIALBUTTONS", 1);
            $baseurl = (!empty($_SERVER['HTTPS'])) ? "https://" : "http://";
            $script = $baseurl . 's7.addthis.com/js/250/addthis_widget.js#pubid=' . $this->params->get('addthis-id', '');

            // use template stylesheet if it exists
            $builtin_path = '/plugins/content/roksocialbuttons/assets/roksocialbuttons.css';
            $template_path = '/templates/' . $template . '/html/plg_roksocialbuttons/roksocialbuttons.css';
            $template_stylesheet = JPATH_SITE . $template_path;

            if (file_exists($template_stylesheet)) $stylesheet = JURI::base(true) . $template_path;
            else $stylesheet = JURI::base(true) . $builtin_path;

            //get css from template or plugin
            $document->addStyleSheet($stylesheet);
            $document->addScript($script);
        }
    }

    /**
     * @param $row
     * @return string
     */
    protected function _getCode(&$row){

        //url
        $baseurl = (!empty($_SERVER['HTTPS'])) ? "https://" . $_SERVER['HTTP_HOST'] : "http://" . $_SERVER['HTTP_HOST'];
        if ($_SERVER['SERVER_PORT'] != "80")
           $baseurl .= ":" . $_SERVER['SERVER_PORT'];
        $host = $_SERVER['HTTP_HOST'];
        //for k2 compatibility
        if( ! class_exists('ContentBuildRoute') ) {
            require_once(JPATH_ROOT.'/components/com_content/helpers/route.php');
        }
        $path = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catid));
        $url = $baseurl . $path;
        $class = isset($module_params['class']) ? $module_params['class'] : '';

         $code = '
             <div class="roksocialbuttons addthis_toolbox ' . $class . ' ' . $this->params->get('extra_class', '') . '"
                addthis:url="' . $url . '"
                addthis:title="' . $row->title . '">
             <div class="custom_images">';
         if (trim($this->params->get('prepend_text', '')) != "") {
             $code .= '<h4>' . $this->params->get('prepend_text') . '</h4>';
         }
         if ($this->params->get('enable_twitter', 1)) {
             $code .= '<a class="addthis_button_twitter"><span></span></a>';
         }
         if ($this->params->get('enable_facebook', 1)) {
             $code .= '<a class="addthis_button_facebook"><span></span></a>';
         }
         if ($this->params->get('enable_google', 1)) {
             $code .= '<a class="addthis_button_google"><span></span></a>';
         }
         $code .= '
             </div>
             </div>';

        return $code;
    }

    /**
     * @param $row
     * @param $text
     * @return bool
     */
    protected function _setContentText(&$row, $text)
    {
        if (!isset($row->text) && isset($row->introtext)) {
            $row->introtext = $text;
        } else {
            $row->text = $text;
        }
        return true;
    }

}
