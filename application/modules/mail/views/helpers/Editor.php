<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage View
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Editor.php 48 2010-02-12 13:23:39Z AntonShevchuk $
 */

/** Zend_Dojo_View_Helper_Textarea */
require_once 'Zend/Dojo/View/Helper/Textarea.php';

/** Zend_Json */
require_once 'Zend/Json.php';

/**
 * Dojo Editor dijit
 *
 * @uses       Zend_Dojo_View_Helper_Textarea
 * @package    Zend_Dojo
 * @subpackage View
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see http://framework.zend.com/issues/browse/ZF-6753
 */
class Mail_View_Helper_Editor extends Zend_Dojo_View_Helper_Textarea
{
    /**
     * @param string Dijit type
     */
    protected $_dijit = 'dijit.Editor';

    /**
     * @var string Dijit module to load
     */
    protected $_module = 'dijit.Editor';

    /**
     * JSON-encoded parameters
     * @var array
     */
    protected $_jsonParams = array('captureEvents', 'events', 'plugins');

    /**
     * dijit.Editor
     *
     * @param  string $id
     * @param  string $value
     * @param  array $params
     * @param  array $attribs
     * @return string
     */
    public function editor($id, $value = null, $params = array(), $attribs = array())
    {
        $hiddenName = $id;
        if (array_key_exists('id', $attribs)) {
            $hiddenId = $attribs['id'];
        } else {
            $hiddenId = $hiddenName;
        }
        $hiddenId = $this->_normalizeId($hiddenId);

        $hiddenAttribs = array(
            'id'    => $hiddenId,
            'name'  => $hiddenName,
            'value' => $value,
            'type'  => 'hidden',
        );

        $editorAttribs = array(
            'id'    => $hiddenId . '-Editor',
            'name'  => $this->_normalizeEditorName($hiddenName),
            'type'  => ''
        );

        $editorAttribs = $this->_prepareDijit($editorAttribs, $params, 'textarea');

        $this->_createGetParentFormFunction();
        $this->_createEditorOnSubmit($hiddenId, $hiddenId . '-Editor');

        $html = '<input' . $this->_htmlAttribs($hiddenAttribs) . $this->getClosingBracket()
              . '<div' . $this->_htmlAttribs($editorAttribs) .'>'.$value.'</div>';

        return $html;
    }

    /**
     * Normalize editor element name
     *
     * @param  string $name
     * @return string
     */
    protected function _normalizeEditorName($name)
    {
        if ('[]' == substr($name, -2)) {
            $name = substr($name, 0, strlen($name) - 2);
            $name .= '[Editor][]';
        } else {
            $name .= '[Editor]';
        }
        return $name;
    }

    /**
     * Create onSubmit binding for element
     *
     * @param  string $hiddenId
     * @param  string $editorId
     * @return void
     */
    protected function _createEditorOnSubmit($hiddenId, $editorId)
    {
        $this->dojo->onLoadCaptureStart();
        echo <<<EOJ
function() {
    var form = zend.findParentForm(dojo.byId('$hiddenId'));
    dojo.connect(form, 'onsubmit', function () {
        dojo.byId('$hiddenId').value = dijit.byId('$editorId').getValue(false);
    });
}
EOJ;
        $this->dojo->onLoadCaptureEnd();
    }
}
