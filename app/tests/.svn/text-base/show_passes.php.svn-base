<?php
if (! defined('SIMPLE_TEST')) {
    define('SIMPLE_TEST', 'simpletest/');
}
require_once(SIMPLE_TEST . 'reporter.php');

class ShowPasses extends CakeHtmlReporter {

    function ShowPasses() {
        $this->HtmlReporter();
    }
    function paintPass($message) {
        parent::paintPass($message);
        print "<span class=\"pass\">Pass</span>: ";
        $breadcrumb = $this->getTestList();
        array_shift($breadcrumb);
        print $breadcrumb[1];
        print "->$breadcrumb[2]<br/>\n";
    }
     function paintFail($message) {
            //parent::paintFail($message);
            print "<span class=\"fail\">Fail</span>: ";
            $breadcrumb = $this->getTestList();
            array_shift($breadcrumb);
            print $breadcrumb[1];
            print "->$breadcrumb[2]";
            
            print " -&gt; " . $this->_htmlEntities($message) . "<br />\n";
        }
     function _getCss() {
        return parent::_getCss() . ' .pass { color: green; }'
        .'.fail {color: red; font-weight: bold; font-size: 120%;}';
    }


}

?>
