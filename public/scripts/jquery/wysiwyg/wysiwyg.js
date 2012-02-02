/**
 * LICENSE
 *
 * Copyright (c) 2011 NIX Solutions Ltd., http://nixsolutions.com/
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 */

/**
 * Wysiwyg jquery plugin
 *
 * Wysiwyg is a composition of several plugins:
 *  - wysiwyg
 *  - wysiwygToolbar
 *  - colorPick
 *  - yourBrowserSucks
 *
 * @author Maks Slesarenko
 */
(function($){
    /**
     * Editor events map
     *
     */
    var EditorEventsMap = {

        /**
         * Keydown event
         *
         * @param object e
         * @returns false
         */
        keydown: function(e) {
            var isCtrl = (e.ctrlKey || e.metaKey);
            var cmd = param = false;

            switch (e.keyCode) {
                case 9: //tab
                    if (e.shiftKey) {
                        cmd = 'outdent';
                    } else {
                        cmd = 'indent';
                    }
                    break;
                case 66: //b
                    if (isCtrl) {
                        cmd = param = 'bold';
                    }
                    break;
                case 73://i
                    if (isCtrl) {
                        cmd = param = 'italic';
                    }
                    break;
                case 85: //u
                    if (isCtrl) {
                        cmd = param = 'underline';
                    }
                    break;
            }

            if (cmd) {
                e.preventDefault();
                this._exec(cmd, param);
                return false; //prevent default browser action
            }
        }
    };

    /**
     * Editor commands map
     */
    var EditorCommandsMap = {

        /**
         * For IE
         */
        insertPlaceholder: function() {
            EditorCommandsMap.replacePlaceholder.call(this, '');
            this._exec('inserthtml', '<span id="placeHolder"/>');
        },

        /**
         * For IE
         *
         * @param html
         */
        replacePlaceholder: function(html) {
            $($(this.element).data('frameDoc')).find('#placeHolder').after(html).remove();
        },

        /**
         * Justify text
         *
         * @param string direction
         */
        justify: function(direction) {
            if (direction) {
                if (direction) {
                    direction = direction.charAt(0).toUpperCase()
                              + direction.slice(1).toLowerCase();
                }
                switch (direction) {
                    case 'Left':
                    case 'Right':
                    case 'Center':
                        this._exec('justify' + direction);
                        break;
                    default:
                        $.error('Unknown justify type - ' + direction);

                }
            }
        },

        /**
         * Strike text
         */
        strike: function() {
            this._exec('strikeThrough');
        },

        /**
         * Wrap text with html tag
         *
         * @param string tagName
         */
        format: function (tagName) {
            tagName = tagName.toLowerCase();

            if ($.browser.msie) {
                tagName = '<' + tagName + '>';
            }
            if (tagName) {
                this._exec('formatBlock', tagName);
            }
        },

        /**
         *  Insert new html tag
         *
         *  @param string tagName
         */
        tag: function(tagName) {
            tagName = tagName.toLowerCase();

            if ('br' == tagName || 'hr' == tagName) {
                this._exec('inserthtml', '<' + tagName + ' />&nbsp;');
            }
        },

        /**
         * Quote Text
         *
         * @todo add quote types
         */
        quote: function() {
            var selected = this.selected();

            if (selected) {
                if ('"' == selected.charAt(0) || '"' == selected.charAt(selected.length -1)) {
                    selected = selected.replace(/^"*/gim, '').replace(/"*$/gim, '');
                } else {
                    selected = '"' + selected + '"';
                }
                this._exec('inserthtml', selected);
            } else {
                this._exec('inserthtml', '"&nbsp;"');
            }
        },

        /**
         * Insert list
         *
         * @param boolen ordered
         */
        list: function(ordered) {
            if (ordered) {
                this._exec('InsertOrderedList');
            } else {
                this._exec('InsertUnorderedList');
            }
        },


        insertImage: function(params) {
            if ("string" == typeof params) {
                params = {src: params};
            }
            if (!params.alt && params.title) {
                params.alt = params.title;
            }
            var html = '';
            for (var i in params) {
                html += i + '="' + params[i] + '" ';
            }
            html = '<img '+ html + '/>';

            if ($.browser.msie) {
                this.exec('replacePlaceholder', html);
            } else {
                this._exec('inserthtml', html);
            }
        },

        image: function() {
            var editor = this;

            if ($.browser.msie) {
                editor.exec('insertPlaceholder');
            }

            var $el = $('#wysiwyg-image-form');
            if ($el.length) {
                $el.dialog('destroy');
                $el.remove();
            }
            $('<div>').load(this._getPath() + 'image.html', function() {
                var $el = $(this).children();

                $el.dialog($.extend({
                    modal: true,
                    buttons: {
                        OK: function() {
                            var url = $('#wysiwyg-image-url').val();
                            if (url) {
                                editor.exec('insertImage', {
                                        src: url,
                                        title: $('#wysiwyg-image-title').val(),
                                        align: $('#wysiwyg-image-align').val()
                                    }
                                );
                            }
                            $el.dialog('close');
                        },
                        Cancel: function() {
                            $el.dialog('close');
                        }
                    }
                }, editor.options.plugins.dialog));

                $('#wysiwyg-image-upload').imageUpload($.extend({
                    callback: function(url) {
                        $('#wysiwyg-image-url').val(url);
                    },
                }, editor.options.plugins.imageUpload));
            });
        },

        /**
         * Wrap selected text with url or unwrap
         *
         * @param string|null url
         */
        link: function () {
            var selected = this.selected();

            if (selected) {
                var url = prompt('Enter url', 'http://');

                if (url) {
                    this._exec('createLink', url);
                } else {
                    this._exec('unlink');
                }
            }
        },

        /**
         * Insert html
         *
         * @param string html
         */
        html: function(html) {
            this._exec('inserthtml', html);
        },

        /**
         * Remove all formatting from selected text
         */
        clean: function() {
            this._exec('removeFormat');
        },

        /**
         * Check if font size equals to param
         *
         * @param number size
         * @returns boolen
         */
        isFontSize: function(size) {
            return EditorCommandsMap._checkFont(this.activeNode(), 'size', 'fontSize', size);
        },

        /**
         * Check if font family equals to param
         *
         * @param string face
         * @returns boolen
         */
        isFontFace: function(face) {
            return EditorCommandsMap._checkFont(this.activeNode(), 'face', 'fontFamily', face);
        },

        /**
         * Check if node font attribute or style equal to value recursively
         *
         * @param object node
         * @param string attrName
         * @param string propertyName
         * @param mixed value
         * @returns boolen
         */
        _checkFont: function(node, attrName, propertyName, value) {
            if (node && 'html' != node.tagName.toLowerCase()) {
                if ('font' == node.tagName.toLowerCase() && node.getAttribute(attrName)) {
                    return value == node.getAttribute(attrName);
                } else {
                    if (value === node.style[propertyName]) {
                        return true;
                    } else {
                        if (!node.style[propertyName]) {
                            return this._checkFont(node.parentNode,  attrName, propertyName, value);
                        }
                    }
                }
            }
            return false;
        },

        /**
         * Check style of the node  equal to value recursively
         * @param node
         * @param name
         * @param value
         * @returns
         */
        _checkStyle: function (node, property, tag, value) {
            var tagName = node.tagName.toLowerCase();

            if (node && 'html' != tagName) {
                if (value === node.style[property]) {
                    return true;
                } else {
                    if (tag && !$.isArray(tag)) {
                        tag = [tag];
                    }
                    if ($.isArray(tag)) {
                        for (var i = 0; i < tag.length; i++) {
                            if (tag[i] == tagName) {
                                return true;
                            }
                        }
                    }
                    if (!node.style[property]) {
                        return this._checkStyle(node.parentNode, property, tag, value);
                    }
                }
            }
            return false;
        },

        /**
         * Check if selected text is bold
         *
         * @returns boolen
         */
        isBold: function() {
            return EditorCommandsMap._checkStyle(this.activeNode(), 'fontWeight', ['b', 'strong'], 'bold');
        },

        /**
         * Check if selected text is italic
         *
         * @returns boolen
         */
        isItalic: function() {
            return EditorCommandsMap._checkStyle(this.activeNode(), 'fontStyle', ['i', 'em'],'italic');
        },

        /**
         * Check if selected text is underlined
         *
         * @returns boolen
         */
        isUnderline: function() {
            return EditorCommandsMap._checkStyle(this.activeNode(), 'textDecoration', 'u', 'underline');
        },

        /**
         * Check if selected text is justified to direction italic
         *
         * @param string direction
         * @returns boolen
         */
        isJustify: function(direction) {
            return EditorCommandsMap._checkStyle(this.activeNode(), 'textAlign', null, direction.toLowerCase());
        }
    };

    var buttonsMap = {
        b: {
            status: "isBold",
            title: "Bold",
            'primary-icon': "gpui-icon gpui-icon20",
            cmd: "bold"
        },
        i: {
            status: "isItalic",
            title: "Italic",
            'primary-icon': "gpui-icon gpui-icon114",
            cmd: "italic"
        },
        u: {
            status: "isUnderline",
            title: "Underline",
            'primary-icon': "gpui-icon gpui-icon187",
            cmd: "underline"
        },
        biu: [
            'b', 'i', 'u'
        ],
        indent: {
            title: "Indent",
            'primary-icon': "gpui-icon gpui-icon204",
            cmd: "indent"
        },
        outdent: {
            title: "Outdent",
            'primary-icon': "gpui-icon gpui-icon205",
            cmd: "outdent"
        },
        olist: {
            title: "Ordered list",
            'primary-icon': "gpui-icon gpui-icon201",
            cmd: "list",
            param: 'true'
        },
        ulist: {
            title: "Outdent",
            'primary-icon': "gpui-icon gpui-icon120",
            cmd: "list"
        },
        justifyLeft: {
            status: "isJustify",
            title: "Justify left",
            'primary-icon': "gpui-icon gpui-icon138",
            cmd: "justify",
            param: 'left'
        },
        justifyCenter: {
            status: "isJustify",
            title: "Justify center",
            'primary-icon': "gpui-icon gpui-icon140",
            cmd: "justify",
            param: 'center'
        },
        justifyRight: {
            status: "isJustify",
            title: "Justify right",
            'primary-icon': "gpui-icon gpui-icon139",
            cmd: "justify",
            param: 'right'
        },
        justify: [
            'justifyLeft', 'justifyCenter', 'justifyRight'
        ],
        link: {
            title: "Insert link",
            'primary-icon': "gpui-icon gpui-icon119",
            cmd: "link",
            param: true
        },
        unlink: {
            title: "Remove link",
            'primary-icon': "gpui-icon gpui-icon25",
            cmd: "unlink"
        },
        linkToggle: [
            'link', 'unlink'
        ],
        image: {
            title: "Insert image",
            'primary-icon': "gpui-icon gpui-icon148",
            cmd: "image"
        },
        removeFormat: {
            title: "Remove formatting",
            'primary-icon': "gpui-icon gpui-icon83",
            cmd: "removeFormat"
        },
        p: {
            title: "Insert <p>",
            cmd: "format",
            html: 'P'
        },
        quote: {
            title: "Insert quotes",
            cmd: "quote",
            html: '&quot;'
        },
        br: {
            title: "Insert <br>",
            cmd: "tag",
            param: 'br',
            html: 'BR'
        },
        heading1: {
            cmd: "format",
            param: "h1",
            title: "Insert &lt;h1&gt;",
            html: "<h1>Heading 1</h1>"
        },
        heading2: {
            cmd: "format",
            param: "h2",
            title: "Insert &lt;h2&gt;",
            html: '<h2>Heading 2</h2>'
        },
        heading3: {
            cmd: "format",
            param: "h3",
            title: "Insert &lt;h3&gt;",
            html: "<h2>Heading 3</h3>"
        },
        heading4: {
            cmd: "format",
            param: "h4",
            title: "Insert &lt;h4&gt;",
            html: "<h2>Heading 4</h4>"
        },
        heading5: {
            cmd: "format",
            param: "h5",
            title: "Insert &lt;h5&gt;",
            html: "<h2>Heading 5</h5>"
        },
        heading6: {
            cmd: "format",
            param: "h6",
            title: "Insert &lt;h6&gt;",
            html: "<h6>Heading 6</h6>"
        },
        pre: {
            cmd: "format",
            param: "pre",
            title: "Insert &lt;pre&gt;",
            html: "<pre>Code</pre>"
        },
        blockquote: {
            cmd: "format",
            param: "blockquote",
            title: "Insert &lt;blockquote&gt;",
            html: "<blockquote>Blockquote</blockquote>"
        },
        fontFaceSerif: {
            cmd: "fontName",
            param: "serif",
            status: "isFontFace",
            style: "font-family: serif",
            html: "Serif"
        },
        fontFaceSansSerif: {
            cmd: "fontName",
            param: "sans-serif",
            status: "isFontFace",
            style: "font-family: sans-serif",
            html: "Sans Serif"
        },
        fontFaceMonospace: {
            cmd: "fontName",
            param: "monospace",
            status: "isFontFace",
            style: "font-family: monospace",
            html: "Monospace"
        },
        fontFaceArial: {
            cmd: "fontName",
            param: "Arial",
            status: "isFontFace",
            style: "font-family: Arial",
            html: "Arial"
        },
        fontFaceHelvetica: {
            cmd: "fontName",
            param: "Helvetica",
            status: "isFontFace",
            style: "font-family: Helvetica",
            html: "Helvetica"
        },
        fontFacePalatino: {
            cmd: "fontName",
            param: "Palatino",
            status: "isFontFace",
            style: "font-family: Palatino",
            html: "Palatino"
        },
        fontFaceGaramond: {
            cmd: "fontName",
            param: "Garamond",
            status: "isFontFace",
            style: "font-family: Garamond",
            html: "Garamond"
        },
        fontFaceBookman: {
            cmd: "fontName",
            param: "Bookman",
            status: "isFontFace",
            style: "font-family: Bookman",
            html: "Bookman"
        },
        fontFaceAvantGarde: {
            cmd: "fontName",
            param: "Avant Garde",
            status: "isFontFace",
            style: "font-family: Avant Garde",
            html: "Avant Garde"
        },
        fontFaceVerdana: {
            cmd: "fontName",
            param: "Verdana",
            status: "isFontFace",
            style: "font-family: Verdana",
            html: "Verdana"
        },
        fontFaceGeorgia: {
            cmd: "fontName",
            param: "Georgia",
            status: "isFontFace",
            style: "font-family: Georgia",
            html: "Georgia"
        },
        fontFaceComicSansMS: {
            cmd: "fontName",
            param: "Comic Sans MS",
            status: "isFontFace",
            style: "font-family: Comic Sans MS",
            html: "Comic Sans MS"
        },
        fontFaceTrebuchetMS: {
            cmd: "fontName",
            param: "Trebuchet MS",
            status: "isFontFace",
            style: "font-family: Trebuchet MS",
            html: "Trebuchet MS"
        },
        fontFaceArialBlack: {
            cmd: "fontName",
            param: "Arial Black",
            status: "isFontFace",
            style: "font-family: Arial Black",
            html: "Arial Black"
        },
        fontFaceImpact: {
            cmd: "fontName",
            param: "Impact",
            status: "isFontFace",
            style: "font-family: Impact",
            html: "Impact"
        },
        fontSize1: {
            cmd: "fontSize",
            param: "1",
            status: "isFontSize",
            html: '<span style="font-size: x-small">The smallest</span>'
        },
        fontSize2: {
            cmd: "fontSize",
            param: "2",
            status: "isFontSize",
            html: '<span style="font-size: small">Small</span>'
        },
        fontSize3: {
            cmd: "fontSize",
            param: "3",
            status: "isFontSize",
            html: '<span style="font-size: medium">Medium</span>'
        },
        fontSize4: {
            cmd: "fontSize",
            param: "4",
            status: "isFontSize",
            html: '<span style="font-size: large">Large</span>'
        },
        fontSize5: {
            cmd: "fontSize",
            param: "5",
            status: "isFontSize",
            html: '<span style="font-size: x-large">Large X</span>'
        },
        fontSize6: {
            cmd: "fontSize",
            param: "6",
            status: "isFontSize",
            html: '<span style="font-size: xx-large">Large XX</span>'
        },
        formatBlock: {
            title: "Format block",
            'secondary-icon': "ui-icon ui-icon-triangle-1-s",
            html: "Format",
            slider:  {
                style: 'width: 160px;',
                buttons: [
                    'heading1',
                    'heading2',
                    'heading3',
                    'heading4',
                    'heading5',
                    'heading6',
                    'pre',
                    'blockquote'
                ]
            }
        },
        fontFace: {
           title: "Change font face",
           'primary-icon': "ui-icon gpui-icon gpui-icon93",
           'secondary-icon': "ui-icon ui-icon-triangle-1-s",
           html: '<span class="toolbar-dropdown-label"></span>',
           style: "min-width:120px;",
           slider: {
               buttons: [
                   'fontFaceSerif',
                   'fontFaceSansSerif',
                   'fontFaceMonospace',
                   'fontFaceArial',
                   'fontFaceHelvetica',
                   'fontFacePalatino',
                   'fontFaceGaramond',
                   'fontFaceBookman',
                   'fontFaceAvantGarde',
                   'fontFaceVerdana',
                   'fontFaceGeorgia',
                   'fontFaceComicSansMS',
                   'fontFaceTrebuchetMS',
                   'fontFaceArialBlack',
                   'fontFaceImpact'
               ]
           }
       },
       fontSize: {
           title: "Change font size",
           'primary-icon': "ui-icon gpui-icon gpui-icon94",
           'secondary-icon': "ui-icon ui-icon-triangle-1-s",
           html: '<span class="toolbar-dropdown-label"></span>',
           style: "min-width:120px",
           slider: {
               style: "width:200px",
               buttons: [
                   'fontSize1',
                   'fontSize2',
                   'fontSize3',
                   'fontSize4',
                   'fontSize5',
                   'fontSize6'
               ]
           }
       },
       hiliteColor: {
           title: "Change font background color",
           'primary-icon': "ui-icon gpui-icon gpui-icon203",
           'secondary-icon': "ui-icon ui-icon-triangle-1-s",
           html: '<span class="toolbar-dropdown-label"></span>',
           slider: {
               style: "width:213px; padding: 10px",
               html: '<div class="toolbar-colorpicker" name="hiliteColor"></div>'
           }
       },
       foreColor: {
           title: "Change font color",
           'primary-icon': "ui-icon gpui-icon gpui-icon202",
           'secondary-icon': "ui-icon ui-icon-triangle-1-s",
           slider: {
               style: "width:213px; padding: 10px",
               html: '<div class="toolbar-colorpicker" name="foreColor"></div>'
           }
       },
       toolbar1: [
           'biu',
           ['indent', 'outdent'],
           'justify',
           'linkToggle',
           'image',
           'removeFormat'
       ],
       toolbar2: [
           ['p', 'quote', 'br'],
           'formatBlock',
           'fontFace',
           'fontSize',
           'hiliteColor',
           'foreColor'
       ]
    };

    /**
     * Wysiwyg widget
     */
    $.widget("zfcore.wysiwyg", {

        /**
         * Widget private API goes below
         */


        /**
         * Check browser compatibility
         *
         * @returns {Boolean}
         */
        _checkBrowser: function() {
            var version = parseInt($.browser.version);
            if ($.browser.msie && version < 8) {
                return false;
            }
            if ($.browser.opera && version < 10) {
                return false;
            }
            if ($.browser.chrome && version < 10) {
                return false;
            }
            return true;
        },

        /**
         * Add/remove jquery ui widget css classes to editor
         *
         */
        _decorate: function() {
            var $widget = $(this.widget());

            if (this.options.ui) {
                $widget.addClass('ui-widget ui-widget-content ui-corner-all');
             } else {
                $widget.removeClass('ui-widget ui-widget-content ui-corner-all');
            }
        },

        /**
         * Set options
         *
         */
        _setOptions: function() {
            var ui = this.options.ui;
            var resizable = this.options.resizable;

            $.Widget.prototype._setOptions.apply(this, arguments); // parent method

            if (ui != this.options.ui) {
                this._decorate();
            }
            if (resizable != this.options.resizable) {
                this._resizable();
            }
        },

        /**
         * Set option
         *
         * @param string key
         * @param mixed value
         */
        _setOption: function (key, value) {
            $.Widget.prototype._setOption.apply(this, arguments); // parent method

            if ('eventsMap' === key) {
                this._bindEventsMap();
            }
        },

        /**
         * Bind editor with events from eventsMap
         */
        _bindEventsMap: function() {
            if (this.options.eventsMap) {
                var doc = $(this.element).data('frameDoc');
                if (doc) {
                    var _self = this;
                    var $doc = $(doc).unbind('.' + this.options.eventNamespace);

                    for (var i in this.options.eventsMap) {
                        $doc.bind(i + '.' + this.options.eventNamespace, function(e) {
                            return _self.options.eventsMap[i].apply(_self, arguments);
                        });
                    }
                }
            }
        },

        /**
         * Enable/disable resizing
         */
        _resizable: function() {
            var $widget = $(this.widget());
            var _self = this;

            if (_self.options.resizable) {
                var $element = $(_self.element);
                var $frame = $element.data('frame');

                $frame = $frame.add($frame.closest('.' + _self.options.frameWrapperClass))
                               .add($widget.find('.' + _self.options.frameOverlayClass));

                $widget.resizable(
                    $.extend({}, _self.options.resizable, {
                        start: function(event, ui) {
                            //$frame.hide();
                            _self._frameOverlay(true);
                        },
                        stop: function(event, ui) {
                            //$frame.show();
                            _self._frameOverlay(false);
                        },
                        alsoResize: $frame
                    }
                ));
                setInterval(function(){
                    if (!$element.is(':visible')) {
                        $element.width($widget.width());
                        $element.height($widget.height());
                        return;
                    } else {
                        $widget.width($element.width());
                        $widget.height($element.height());
                        $widget.resize();

                    }
                    var height = 0;
                    _self.toolbar().each(function() {
                        var $this = $(this);
                        height += $this.height();
                    });

                    $frame.height($widget.height() - height -1);

                }, 2000);
            } else {
                $widget.resizable('disable');
            }
        },

        /**
         * Overlay frame
         *
         * @param boolen showOrHide
         */
        _frameOverlay: function(showOrHide) {
            var $widget = $(this.widget());
            $overlay = $widget.find('.' + this.options.frameOverlayClass);

            $overlay.toggle(showOrHide);
        },

        /**
         * Get selected text
         */
        _getSelected: function() {
            var fwindow = $(this.element).data('frame').get(0).contentWindow;

            if (fwindow.getSelection){ // Gecko, Opera, IE < 9
                var selection = fwindow.getSelection();
                return selection.toString();
             } else if (fwindow.document.selection.createRange) { // MSIE
                var range = fwindow.document.selection.createRange();
                return range.text;
             };
        },

        /**
         * Init widget
         */
        _init: function() {
            var _self = this;
            var options = this.options;
            var $element = $(this.element);


            if ($element.data('frameDoc')) {
                this.html($element.val());
                $element.hide().next('ul.' + options.wrapperClass).show();
            }

            if (options.autosave.url && options.autosave.enabled) {
                $element.data('saveIntervalID', setInterval(function() {
                    var html = _self.html();
                    $.post(options.autosave.url, { data: html });
                }, options.autosave.interval * 1000));

            }

            //auto update element
            $element.data('updateIntervalID', setInterval(function() {
                var html = _self.html();
                if ($element.val() != html) {
                    $element.val(html);
                }
            }, 1500));

            //if ($.browser.msie) {
                //this._exec('formatBlock', '<p>');
            //}

            $(this.widget()).width($element.width()).height($element.height());
        },

        /**
         * Create widget
         */
        _create: function() {
            var _self = this;
            var options = this.options;
            var $element = $(this.element); // this.element

            var wpapper = $('<ul>', {
                'class': options.wrapperClass,
                style: 'list-style-type:none'
            });

            var $frame = $('<iframe>', {
                scrolling: "auto",
                frameborder: "0",
                hspace: "0",
                vspace: "0",
                marginwidth:"0",
                marginheight: "0",
                width: '100%',
                height: '100%'
            });

            var $overlay = $('<div>', {
                'class': options.frameOverlayClass,
                width: '100%',
                height: '100%'
            });

            if (!this._checkBrowser()) {
                $element.yourBrowserSucks(this.options.plugins.yourBrowserSucks);
            }

            $element.hide().after(
                 wpapper.append(
                     $('<li>').addClass(options.frameWrapperClass).append($frame).append($overlay)
                 )
            );

            var frame = $frame.get(0);
            var frameDoc;

            if (frame.contentDocument) {
                frameDoc = frame.contentDocument;
            } else if (frame.contentWindow && frame.contentWindow.document)  {
                frameDoc = frame.contentWindow.document;
            } else {
                frameDoc = frame.document;
            }

            var template = options.template;
            if (options.css) {
                template = template.replace('<head>', '<head><link rel="stylesheet" media="screen" type="text/css" href="' + options.css + '"/>');
            }
            var value = $element.val();
            if (value) {
                var pattern = /^</g;
                if (!pattern.test(value)) {
                    value = '<p>' + value + '</p>';
                }
                template = template.replace('<body><p></p></body>', '<body>' + value + '</body>');
            }

            frameDoc.open();
            frameDoc.write(template);
            frameDoc.close();

            if (options.css) {
                var head = frameDoc.getElementsByTagName("head")[0];
                var link = frameDoc.createElement("link");
                    link.setAttribute("rel", "stylesheet");
                    link.setAttribute("type", "text/css");
                    link.setAttribute("href", options.css);
                    head.appendChild(link);
            }

            frameDoc.designMode = 'on';

            $element.data('frameDoc', frameDoc);
            $element.data('frame', $frame);

            if (options.ui) {
                _self._decorate();
            }
            _self._resizable();

            _self._exec('enableObjectResizing', true);
            _self._exec('enableInlineTableEditing', true);

            if ($.browser.mozilla) {
                _self.exec('useCSS');
                _self.exec('styleWithCSS');
            }

            _self._bindEventsMap();
        },

        /**
         * Execute command
         *
         * @see https://developer.mozilla.org/en/Rich-Text_Editing_in_Mozilla
         *
         * @param string cmd
         * @param mixed param
         */
        _exec: function(cmd, param) {
            var doc = $(this.element).data('frameDoc');
            var $frame = $(this.element).data('frame');

            if (doc) {

                try {
                    $frame.get(0).contentWindow.focus();

                    if (cmd == 'inserthtml' && $.browser.msie) {
                        doc.selection.createRange().pasteHTML(param);
                    } else {
                        doc.execCommand(cmd, false, param);

                        if (param == "blockquote" || param == 'pre') {
                            doc.body.appendChild(doc.createElement("BR"));
                        }
                        //$frame.focus();
                    }
                } catch (e) {
                    if (window.console) {
                        console.log(e);
                    }
                }
            }
        },

        /**
         * Get path to wysiwyg folder
         *
         * @returns string
         */
        _getPath: function() {
            if (!this.options.path) {
                var $el = $('script[src*="wysiwyg\.js"]');
                if ($el.length) {
                    this.options.path = $el[0].src.replace(/wysiwyg\.js/, '');
                } else {
                    $.error('Could not determine path');
                }
            }
            return this.options.path;
        },

        /**
         * Widget public API
         *
         * $('element').wysiwyg('methodName', option1, option2, ...)
         * $('element').wysiwyg({option1:value1, ..})
         */


        /**
         * Mix new command handlers to commands map
         *
         * if map is null - remove commandsMap
         *
         * @param object|null map
         */
        commands: function(map) {
            if (map) {
                $.extend(this.options.commandsMap, map);
            } else if (null === map) {
                this.options.commandsMap = null;
            }
        },

        /**
         * Mix new event handlers to eventsMap
         *
         * if map is null - remove eventsMap
         *
         * @param object|null map
         */
        events: function(map) {
            if (map) {
                $.extend(this.options.eventsMap, map);
            } else if (null === map) {
                this.options.eventsMap = null;
            }
        },

        /**
         * Remove empty tags
         *
         * lowercase tags in ie
         */
        clean: function() {
            var html = this.html();

            html = html.replace(/[    ]*/g, '');
            html = html.replace(/[\r\n]*/g, '');
            html = html.replace(/\n\s*\n/g, "\n");
            html = html.replace(/^[\s\n]*/, '');
            html = html.replace(/[\s\n]*$/, '');

            // indenting
            html = html.replace(/<li/gi, "\t<li");
            html = html.replace(/<tr/gi, "\t<tr");
            html = html.replace(/<td/gi, "\t\t<td");
            html = html.replace(/<\/tr>/gi, "\t</tr>");

            // empty tags
            var tags = ["pre","blockquote","ul","ol","li","table","tr","span", "strong", "i", "b", "u", "p", "div"];

            var length = 0;
            while (length != html.length) {
                length = html.length;
                for (i = 0; i < tags.length; ++i) {
                    var tagName = tags[i];
                    html = html.replace(new RegExp('<' + tagName + '[^>]*>\s*(<br[^>]*>|&nbsp;|)\s*</' + tagName + '>','gim'), "");
                }
            }

            var lb = '\r\n';
            var tags = ["html", "head", "title", "meta", "link", "style", "body", "div", "p", "form", "fieldset", "label", "legend", "object", "embed", "select", "option", "input","textarea", "br", "hr", "pre", "blockquote", "ul", "ol", "li", "dl", "dt", "dd", "\!--", "table", "thead", "tbody", "caption", "th", "tr", "td", "script", "noscript"];
            for (i = 0; i < tags.length; ++i) {
                var tagName = tags[i];
                html = html.replace(new RegExp('<' + tagName, 'gi'), lb + '<' + tagName);
                html = html.replace(new RegExp('</' + tagName, 'gi'), lb + '</' + tagName);
            }

            if ($.browser.msie) {
                // tags to lowercase
                html = html.replace(/< *(\/ *)?(\w+)/g, function(tagName){ return tagName.toLowerCase() });
            }

            this.html(html);
        },

        /**
         * Get active node
         *
         * @returns object
         */
        activeNode: function() {
            var doc = $(this.element).data('frameDoc');

            if (doc.getSelection) {
                var selection = doc.getSelection();
                if (selection && selection.anchorNode) {
                    return selection.anchorNode.parentNode;
                }
            } else {
                if (document.selection) {   // Internet Explorer before version 9
                    return doc.selection.createRange().parentElement();
                }
                //$.error('getSelection method not found');
            }
        },

        /**
         * Show widget
         */
        show: function() {
            this.hide();
            this._init();
        },

        /**
         * Hide widget
         */
        hide: function() {
            var $element = $(this.element);

            $element.show().next('ul.' + this.options.wrapperClass).hide();

            if ($element.data('updateIntervalID')) {
                clearInterval($element.data('updateIntervalID'));
                $element.data('updateIntervalID', false);
            }
            if ($element.data('saveIntervalID')) {
                clearInterval($element.data('saveIntervalID'));
                $element.data('saveIntervalID', false);
            }
        },

        /**
         * Execute command
         *
         * Commands can be added via commandsMap
         *
         * if commandsMap is set than try it first
         */
        exec: function(cmd, param) {
            if (this.options.commandsMap) {
                if (this.options.commandsMap[cmd]) {
                    return this.options.commandsMap[cmd].apply(this, Array.prototype.slice.call(arguments, 1));
                }
            }
            return this._exec(cmd, param);
        },

        /**
         * Get widget
         */
        widget: function() {
            return $(this.element).next('ul.' + this.options.wrapperClass);
        },

        /**
         * Get selected text
         *
         * @returns string
         */
        selected: function() {
            //TODO if arguments passed create selection
            return this._getSelected();
        },

        /**
         * Get or set editor content
         *
         * @param string html
         * @returns string
         */
        html: function(html) {
            var doc = $(this.element).data('frameDoc');

            if (doc.body) {
                if (typeof(html) != 'undefined') {
                    doc.body.innerHTML = html;
                } else {
                    return doc.body.innerHTML;
                }
            }
        },

        /**
         * Destroy widget
         */
        destroy: function() {
            $.Widget.prototype.destroy.apply(this, arguments); // default destroy

            // now do other stuff particular to this widget
            this.hide();

            this.toolbar().wysiwygToolbar('destroy');

            $(this.widget()).remove();
        },

        /**
         * Enable widget
         */
        enable: function() {
            $.Widget.prototype.enable.apply(this, arguments); // default enable

            var doc = $(this.element).data('frameDoc');
            doc.designMode = 'on';

            this.toolbar().wysiwygToolbar('enable');
        },

        /**
         * Disable widget
         */
        disable: function() {
            $.Widget.prototype.disable.apply(this, arguments); // default disable

            var doc = $(this.element).data('frameDoc');
            doc.designMode = 'off';

            this.toolbar().wysiwygToolbar('disable');
        },

        /**
         * Add or get toolbars
         *
         * @param string|array toolbar
         * @returns array of toolbars in no param specified
         */
        toolbar: function(toolbar) {
            var $widget = $(this.widget());

            if (typeof(toolbar) != 'undefined') {
                var args = {};
                if ($.isPlainObject(this.options.plugins.wysiwygToolbar)) {
                    args = $.extend(args, this.options.plugins.wysiwygToolbar);
                }
                if (typeof(toolbar) == 'string') {
                    if ('#' == toolbar[0] || '.' == toolbar[0]) {
                        toolbar = $(toolbar).wysiwygToolbar(args);
                    } else {
                        args.buttons = toolbar;
                        toolbar = $('<div>').wysiwygToolbar(args);
                    }
                } else if (!(toolbar instanceof $)) {
                    if ($.isArray(toolbar)) {
                        args.buttons = toolbar;
                        toolbar = $('<div>').wysiwygToolbar(args);
                    } else {
                        toolbar = $('<div>').wysiwygToolbar($.extend(args, toolbar));
                    }
                }
                var $li = $('<li>').addClass(this.options.toolbarWrapperClass).append(toolbar);

                var $frame = $widget.children('li.' + this.options.frameWrapperClass);
                if ($frame.length) {
                    $frame.before($li);
                } else {
                    $widget.append($li);
                }

                toolbar.wysiwygToolbar('option' , 'editor', this);

                $frame.height($frame.height() - toolbar.height() - 1/*border*/);

            } else {
                return $widget.children('li.' + this.options.toolbarWrapperClass).children();
            }
        },

        /**
         * Widget options
         *
         * $('element').wysiwyg({optionName: optionValue});
         *
         * or
         *
         * $('element').wysiwyg('option', optionName, optionValue)
         *
         */
        options: {
            path: null, /* base url */
            uploadTemplate: null,
            wrapperClass: 'wysiwyg-wrapper',
            toolbarWrapperClass: 'wysiwyg-toolbar',
            frameWrapperClass: 'wysiwyg-frame',
            frameOverlayClass: 'wysiwyg-frame-overlay',
            eventNamespace: 'zfcorewysiwyg',
            resizable: {handles: 's'},
            ui: true,
            autosave: {
                url: null,
                enabled: true,
                interval: 5
            },
            css: null,
            commandsMap: EditorCommandsMap,
            eventsMap: EditorEventsMap,
            //template: '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html><head></head><body><p></p></body></html>'
            template: '<!DOCTYPE html><html><head></head><body><p></p></body></html>',
            plugins: {}
        }
    });

    /**
     * Wysiwyg toolbar widget
     */
    $.widget("zfcore.wysiwygToolbar", {

        /**
         * Widget private API goes below
         */

        /**
         * Add/remove jquery ui widget css classes to toolbar, enable/disable jquery ui buttons
         *
         */
        _decorate: function() {
            var $widget = $(this.widget());

            if (this.options.ui) {
                $widget.addClass('ui-widget ui-widget-header ui-corner-all');
                $widget.find('a, button, input').each(function(){

                    var $this = $(this);
                    var text = true;
                    if ('false' == $this.attr('text') || '' == $this.html() || '&nbsp;' == $this.html()) {
                        text = false;
                        $this.html('&nbsp;');
                    } else if ('' == $this.text()) {
                        $this.append('&nbsp;');
                    }
                    $this.button({'text': text, icons: {primary: $this.attr('primary-icon'), secondary: $this.attr('secondary-icon')}});
                });
                $widget.children('.' + this.options.buttonsetClass).buttonset();
                $widget.find('.' + this.options.dropdownSliderClass).addClass('ui-state-default ui-corner-bottom');
            } else {
                $widget.removeClass('ui-widget ui-widget-header ui-corner-all');
                $widget.children('a, button, input').button('disable');
                $widget.children('.' + this.options.buttonsetClass).buttonset('disable');
                $widget.find('.' + this.options.dropdownSliderClass).removeClass('ui-state-default ui-corner-bottom');
            }
        },

        /**
         * Set options
         */
        _setOptions: function() {
            var ui = this.options.ui;
            $.Widget.prototype._setOptions.apply(this, arguments); // default disable

            if (ui != this.options.ui) {
                this._decorate();
            }
        },

        /**
         * Create button from options
         *
         * @param options
         * @returns jQuery
         */
        _createButton: function(options) {
            var $button;
            if ('string' == typeof(options)) {
                options = buttonsMap[options];
            }
            var stub = {};
            if (options instanceof Array) {
                stub = [];
            }
            options = $.extend(true, stub, options);

            if (options.length) {
                $button = $('<div>', {'class': this.options.buttonsetClass});
                for (var j in options) {
                    $button.append(this._createButton(options[j]));
                }
            } else {
                var $slider = null;
                if (options.slider) {
                    var $sliderButtons = null;
                    if ('object' == typeof(options.slider)) {
                        if (options.slider.buttons) {
                            $slider = this._createButton(options.slider.buttons);
                            options.slider.buttons = undefined;

                            $slider.removeClass(this.options.buttonsetClass)
                                   .attr(options.slider);
                        } else {
                            $slider = $('<div>', options.slider);
                        }
                    } else {
                        $slider = $('<div>').html(options.slider);
                    }
                    if ($sliderButtons) {
                        $slider.append($sliderButtons);
                    }
                    $slider.addClass(this.options.dropdownSliderClass);
                    options.slider = false;
                }
                var type = 'button';
                if (options.type) {
                    type = options.type;
                    options.type = undefined;
                }
                $button = $('<button type="'+type+'">', options).addClass(this.options.buttonClass);
                for (var i in options) {
                    if (i == 'html' || i == 'text') {
                        $button[i](options[i]);
                    } else {
                        $button.attr(i, options[i]);
                    }

                }

                if (false == options.text) {
                    $button.attr('text', 'false');
                }
                if ($slider) {
                    $button = $('<div>', {'class': this.options.dropdownClass}).append($button).append($slider);
                }
            }
            return $button;
        },

        /**
         * Create widget
         */
        _create: function() {
            var $el = $(this.element);
            var _self = this;
            var options = _self.options;

            if (options.buttons) {
                if ('string' == typeof(options.buttons)) {
                    options.buttons = buttonsMap[options.buttons];
                }
                for (var i in options.buttons) {
                    $el.append(_self._createButton(options.buttons[i]));
                }
            }

            $el.children().data('toolbar', this);
            var buttons = $el.find('.' + options.buttonClass).data('toolbar', this).click(function(){

                var $this  = $(this);
                var action = $this.attr('action');
                var param  = $this.attr('param');
                var cmd    = $this.attr('cmd');

                if (!action) {
                    action = 'exec';
                }
                //if ('undefined' === typeof(param)) {
                //    param = cmd;
                //}
                return _self.editor(action, cmd, param);
            });

            buttons = buttons.filter('[status]');

            if (buttons.length) {

                var $dynamicDropdown = $el.find('.' + options.dropdownLabelClass).closest('.' + options.dropdownClass); //find dropdowns with dynamic labels

                $el.data('statusInterval', setInterval(function() {
                    //change button status (on/off)
                    var editor = _self.editor();
                    if (editor && $el.is(':visible')) {

                        buttons.each(function() {
                            var $this = $(this);
                            $this.toggleClass(options.buttonHighlightClass, editor.exec($this.attr('status'), $this.attr('param')));
                        });
                    }

                    //change dropdown label (icon/value)
                    $dynamicDropdown.each(function () {
                        var $this = $(this);
                        var $activeButton = $this.children('.' + options.dropdownSliderClass).find('.' + options.buttonHighlightClass);

                        var $icon = $this.find('.' + options.dropdownIconClass);
                        var $label = $this.find('.' + options.dropdownLabelClass);

                        if ($activeButton.length) {
                            $icon.hide();
                            $label.show().html($activeButton.text());
                        } else {
                            $icon.show();
                            $label.hide();
                        }
                    });
                }, 1000));
            }

            $el.find("." + options.dropdownClass).find("button, input, a").click(function () {
                var dropdown = $(this).closest('.' + options.dropdownClass);
                    dropdown.children('.' + options.dropdownSliderClass).slideToggle('fast', function(){
                        _self.editor('_frameOverlay', $(this).is(':visible'));
                    });

                    dropdown.toggleClass(options.dropdownActiveClass);
            });

            $(document).bind('click', function (e) {
                $el.find('.' + options.dropdownClass).each(function() {
                    if (e.target.parentNode != this && e.target.parentNode.parentNode != this) {
                        $('.' + options.dropdownSliderClass + ':visible', this).slideUp();
                        $(this).removeClass(options.dropdownActiveClass);
                    }
                });

                _self.editor('_frameOverlay', false);
            });

            $el.find('.' + options.colorPickerClass).colorpick({
                click: function (e, color) {
                    _self.editor('exec', $(this).closest('.' + options.colorPickerClass).attr('name'), '#' + color);
                }
            });

            this._decorate();
        },



        /**
         * Widget public API goes below
         *
         */


        /**
         * Show widget
         */
        show: function() {
            $(this.widget()).show();
        },

        /**
         * Hide widget
         */
        hide: function() {
            $(this.widget()).hide();
        },

        /**
         * Destroy widget
         */
        destroy: function() {
            $.Widget.prototype.destroy.apply(this, arguments); // default destroy

            var intervalId = $(this.element).data('statusInterval')
            if (intervalId) {
                clearInterval(intervalId);
            }
            $(this.widget()).remove();
        },

        /**
         * Call editor method or get editor
         */
        editor: function(cmd) {
            if (cmd) {
                if (!this.options.editor) {
                    $.error('Editor not set');
                }
                this.options.editor[cmd].apply(this.options.editor, Array.prototype.slice.call(arguments, 1));
            }
            return this.options.editor;
        },

        /**
         * Widget options
         *
         * $('element').wysiwygToolbar({optionName: optionValue});
         *
         * or
         *
         * $('element').wysiwygToolbar('option', optionName, optionValue)
         *
         */
        options: {
            buttons: null,
            ui: true,
            buttonClass: 'toolbar-button',
            buttonsetClass: 'toolbar-buttonset',
            buttonHighlightClass: 'ui-state-focus',
            colorPickerClass: 'toolbar-colorpicker',
            dropdownLabelClass: 'toolbar-dropdown-label',
            dropdownIconClass: 'ui-button-icon-primary',
            dropdownClass: 'toolbar-dropdown',
            dropdownSliderClass: 'toolbar-dropdown-slider',
            dropdownActiveClass: 'toolbar-dropdown-active',
            editor: null,
            plugins: {}
        }
    });
    /**
     * Wysiwyg color picker widget
     */
    $.widget("zfcore.colorpick", {

        /**
         * Widget private API goes below
         *
         */

        _map: [
            [
                [
                    '000000', '434343', '666666', '999999', 'B7B7B7', 'CCCCCC', 'D9D9D9', 'EFEFEF', 'F3F3F3', 'FFFFFF'
                ]
            ],
            [
                [
                    '980000', 'FF0000', 'FF9900', 'FFFF00', '00FF00', '00FFFF', '4A86E8', '0000FF', '9900FF', 'FF00FF'
                ]
            ],
            [
                [
                    'E6B8AF', 'F4CCCC', 'FCE5CD', 'FFF2CC', 'FFF2CC', 'D0E0E3', 'C9DAF8', 'CFE2F3', 'D9D2E9', 'EAD1DC'
                ],
                [
                    'DD7E6B', 'EA9999', 'F9CB9C', 'FFE599', 'B6D7A8', 'A2C4C9', 'A4C2F4', '9FC5E8', 'B4A7D6', 'D5A6BD'
                ],
                [
                    'CC4125', 'E06666', 'F6B26B', 'FFD966', '93C47D', '76A5AF', '6D9EEB', '6FA8DC', '8E7CC3', 'C27BA0'
                ],
                [
                    'A61C00', 'CC0000', 'E69138', 'F1C232', '6AA84F', '45818E', '3C78D8', '3D85C6', '674EA7', 'A64D79'
                ],
                [
                    '85200C', '990000', 'B45F06', 'BF9000', '38761D', '134F5C', '285BAC', '0B5394', '351C75', '741B47'
                ],
                [
                    '5B0F00', '660000', '783F04', '7F6000', '274E13', '0C343D', '1C4587', '073763', '20124D', '4C1130'
                ]
            ]
        ],

        /**
         * Create widget
         *
         */
        _create: function() {
            var $el = $(this.element);

            var map = this._map;
            if (this.options.colorMap) {
                map = this.options.colorMap;
            }
            for (var i in map) {
                $el.append(this._arrayToHtml(map[i]));
            }
            this._click(this.options.click);
        },

        /**
         * Set option
         *
         * @param string key
         * @param mixed value
         */
        _setOption: function(key, value) {
            $.Widget.prototype._setOption.apply(this, arguments); // parent method

            if ('click' == key) {
                this._click(value);
            }
        },

        /**
         * Array to html
         *
         * @param object map
         * @returns array
         */
        _arrayToHtml: function(map) {
            var attrs = this.options.attrs;
                attrs.table['class'] =  this.options.classes.table;
                attrs.table.cellspacing = '0';
                attrs.table.cellpadding = '0';
                attrs.tr['class'] = this.options.classes.tr;
                attrs.td['class'] = this.options.classes.td;
                attrs.color['class'] = this.options.classes.color;


            var $table = $('<table>', attrs.table);
            var tr = '';

            for (var i in map) {
                tr = $('<tr>', attrs.tr);

                for (var j in map[i]) {
                    tr.append(
                        $('<td>', attrs.td).append(
                            $('<div>', attrs.color).attr('color', map[i][j]).css('backgroundColor', '#' + map[i][j])
                        )
                    );
                }
                $table.append(tr);
            }
            return $table;
        },

        /**
         * Set click event handler
         *
         * @param function callback
         */
        _click: function(callback) {
            $colors = $(this.element).find('.' + this.options.classes.color);
            if (callback) {
                $colors.bind('click.' + this.options.eventNamespace, function(e) {
                   callback.call(this, e, $(this).attr('color'));
                });
            } else {
                $colors.unbind('click.' + this.options.eventNamespace);
            }
        },



        /**
         * Widget public API goes below
         *
         */


        /**
         * Show widget
         */
        show: function() {
            $(this.widget()).show();
        },

        /**
         * Hide widget
         */
        hide: function() {
            $(this.widget()).hide();
        },

        /**
         * Destroy widget
         */
        destroy: function() {
            $.Widget.prototype.destroy.apply(this, arguments); // default destroy

            $(this.widget()).remove();
        },

        /**
         * Set click event handler
         *
         * @param function callback
         */
        click: function(callback) {
            this._setOption('click', callback);
        },


        /**
         * Widget options
         *
         * $('element').colorpick({optionName: optionValue});
         *
         * or
         *
         * $('element').colorpick('option', optionName, optionValue)
         *
         */
        options: {
            colorMap: null,
            classes:{
                color: 'colorpick-color',
                td:    'colorpick-td',
                tr:    'colorpick-row',
                table: 'colorpick-table'
            },
            attrs: {
                color: {},
                td:    {},
                tr:    {},
                table: {}
            },
            click: null,
            eventNamespace: 'zfcorecolorpick'
        }
    });

    /**
     * Widget to notify user about his old browser
     *
     */
    $.widget("zfcore.overlay", {

        /**
         * Widget private API goes below
         *
         */
        _widget: null,

        /**
         * Create widget
         */
        _create: function() {
            var $el = $(this.element);

            this._widget = $('<div>').width($el.width())
                                     .height($el.height())
                                     .html(this.options.template)
                                     .css({
                                         position: 'absolute',
                                         top: $el.offset().top,
                                         left: $el.offset().left,
                                         background: '#fff',
                                         zIndex: 99999,
                                         opacity: this.options.opacity
                                     })
                                     .addClass(this.options.overlayClass);

            if ($el.is('body')) {
                this._widget.width($(window).innerWidth())
                            .height($(window).innerHeight())
                            .css({top:0, left: 0});
            }
            this._widget.appendTo(document.body);
        },

        widget: function() {
            return this._widget;
        },

        /**
         * Widget public API goes below
         *
         */

        /**
         * Destroy widget
         */
        destroy: function() {
            $.Widget.prototype.destroy.apply(this, arguments); // default destroy

            this._widget.remove();
        },

        /**
         * Widget options
         *
         * $('element').overlay({optionName: optionValue});
         *
         * or
         *
         * $('element').overlay('option', optionName, optionValue)
         *
         */
        options: {
            template: '',
            opacity: 0.5,
            overlayClass: 'zfcore-overlay'
        }
    });

    /**
     * Widget to notify user about his old browser
     *
     */
    $.widget("zfcore.yourBrowserSucks", $.zfcore.overlay, {

        /**
         * Create widget
         */
        _init: function() {
            var options = this.options;

            $cancel = this._widget.find('.' + options.closeButtonClass);
            if (options.strict) {
                $cancel.remove();
            } else {
                var _self = this;
                $cancel.click(function(){
                    _self.destroy();
                });
            }
            if (this._widget.width() < options.minWidth) {
                this._widget.width(options.minWidth);
            }

            if (this._widget.height() < options.minHeight) {
                this._widget.height(options.minHeight);
            }
        },

        /**
         * Widget options
         *
         * $('element').yourBrowserSucks({optionName: optionValue});
         *
         * or
         *
         * $('element').yourBrowserSucks('option', optionName, optionValue)
         *
         */
        options: {
            opacity: 1,
            minWidth: 360,
            minHeight: 300,
            strict: false,
            closeButtonClass: 'IWantToUseMyBrowser',
            template: '<table border="0" cellspacing="0" cellpadding="25" width="100%" height="100%" align="center" style="border: 1px solid #f00;background: #fff"> \
                           <tr valign="top"> \
                               <td colspan="4"><p>It appears you\'re using an outdated browser which prevents access to some of the features on our website. While it\'s not required, you really should upgrade or install a new browser!</p></td> \
                           </tr> \
                           <tr valign="top"> \
                               <td colspan="4">Visit the official sites for popular browsers below:</td> \
                           </tr> \
                           <tr valign="middle" align="center"> \
                               <td><a href="http://getie.com"><img src="data:image/gif;base64,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%3D%3D"><br />Internet Explorer</a></td> \
                               <td><a href="http://getfirefox.com"><img src="data:image/gif;base64,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%3D%3D"><br />Firefox</a></td> \
                               <td><a href="http://www.google.com/chrome"><img src="data:image/gif;base64,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"><br />Chrome</a></td> \
                               <td><a href="http://www.apple.com/safari"><img src="data:image/gif;base64,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%3D"><br />Opera</a></td> \
                           </tr> \
                           <tr align="center" valign="bottom"><td colspan="4"><a href="javascript:void(0)" class="IWantToUseMyBrowser"><small>No, thanks</small></a></td></tr> \
                       </table>'
        }
    });

    /**
     * Widget to notify user about his old browser
     *
     */
    $.widget("zfcore.imageUpload", {

        /**
         * Widget private API goes below
         *
         */

        _frame: null,

        /**
         * Create widget
         */
        _create: function() {
            var _self = this;
            var $el = $(this.element);
            var value = '';

            if ('file' != $el.attr('type')) {
                $.error('Plugin "imageUpload" must be applied on a input element with type "file"');
            }

            if (!$.isFunction(this.options.callback)) {
                $.error('Callback not set');
            }

            setInterval(function() {
                if ($el.val() && value != $el.val()) {
                    value = $el.val();

                    _self._startUpload();
                }
            }, 1000);
        },

        _startUpload: function() {
            $(this.element).attr('disabled', true);
            $('body').overlay(this.options.plugins.overlay);

            this._frame = this._createFrame();
            var form = this._createForm();
            this._frame.append(form);

            // Watch for a new set of requests
            if (!$.active++) {
                $.event.trigger("ajaxStart");
            }
            // Create the request object
            $.event.trigger("ajaxSend", [this]);

            try {
                form.attr('target', this._frame.attr('id'));
                form.submit();
            } catch(e) {
                $.error('Error submiting form');
            }
            var _self = this;
            this._frame.load(function() {
                _self._uploadCallback();
            });
        },

        _createForm: function() {
            var id = new Date().getTime();
            //create form
            var formId = 'wysiwyg-image-upload-form-' + id;
            var fileId = 'wysiwyg-image-upload-file' + id;

            var form = $('<form>', {
                action: this.options.url,
                method: "POST",
                name: formId,
                id: formId,
                enctype: "multipart/form-data",
                encoding: 'multipart/form-data'
            });

            if (this.options.data) {
                for(var i in this.options.data) {
                    $('<input type="hidden" name="' + i + '" value="' + this.options.data[i] + '" />').appendTo(form);
                }
            }
            var newElement = $(this.element).clone();
            newElement.attr('id', fileId);
            newElement.appendTo(form);

            //set attributes
            form.css('position', 'absolute');
            form.css('top', '-1200px');
            form.css('left', '-1200px');

            return form;
        },

        _createFrame: function() {
            var frameId = 'wysiwyg-image-upload-frame-' + new Date().getTime();

            var $frame = $('<iframe>', {
                id: frameId,
                name: frameId,
                style: "position:absolute; top:-9999px; left:-9999px"
            });

            if (window.ActiveXObject) {
                var urlType = typeof this.options.secureurl;

                if (urlType == 'boolean'){
                    $frame.attr('src', 'javascript:false');
                } else if (urlType == 'string') {
                    $frame.attr('src', this.options.secureurl);
                }
            }
            $frame.appendTo(document.body);

            return $frame;
        },

        _uploadCallback: function() {
            var xml = {};
            var frame = this._frame.get(0);

            if (frame.contentWindow) {
                var frameDoc = frame.contentWindow.document;
                xml.responseText = frameDoc.body ? frameDoc.body.innerHTML : null;
                xml.responseXML  = frameDoc.XMLDocument ? frameDoc.XMLDocument : frameDoc;

            } else if (frame.contentDocument) {
                var frameDoc = frame.contentWindow.document;
                xml.responseText = frameDoc.body ? frameDoc.body.innerHTML : null;
                xml.responseXML  = frameDoc.XMLDocument ? frameDoc.XMLDocument : frameDoc;
            }

            if (!$.isEmptyObject(xml)) {
                try {
                    $.event.trigger("ajaxSuccess", [xml, this]);

                    if (this.options.callback) {
                        this.options.callback.call(this, xml.responseText, xml);
                    }
                } catch(e) {
                    //TODO
                }

                // The request was completed
                $.event.trigger("ajaxComplete", [xml, this]);

                // Handle the global AJAX counter
                if (! --$.active) {
                    $.event.trigger("ajaxStop");
                }

                $(this.element).attr('disabled', false);
                $('body').overlay('destroy');

                var _self = this;

                setTimeout(function() {
                    try {
                        _self._frame.remove();
                    } catch(e) {
                        //
                    }
                }, 100);
            }
        },

        /**
         * Widget public API goes below
         *
         */

        /**
         * Destroy widget
         */
        destroy: function() {
            $.Widget.prototype.destroy.apply(this, arguments); // default destroy

            $(this.element).dialog('destroy');
        },

        /**
         * Widget options
         *
         */
        options: {
            url: null,
            secureurl: false,
            callback: null,
            plugins: {}
        }
    });

    $.zfcore.buttons  = buttonsMap;
    $.zfcore.events   = EditorEventsMap;
    $.zfcore.commands = EditorCommandsMap;
})(jQuery);
