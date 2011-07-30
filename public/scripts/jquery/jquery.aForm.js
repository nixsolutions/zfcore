/**
 * LICENSE
 * 
 * Copyright (c) 2010, Maks Slesarenko
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *   1 Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *   2 Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in the
 *     documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 * The views and conclusions contained in the software and documentation are 
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of Maks Slesarenko.
 */
(function($){
    $.fn.extend({
        //pass the options variable to the function
        aForm: function(options) {
 
            //Set the default values, use comma to separate the settings, example:
            var defaults = {
                requiredMessage: 'This is a required field', //if title not set this is used
                requiredClass: 'aForm-field-required',
                validateClass: 'aForm-field-validate', 
                invalidClass:  'aForm-field-invalid',
                adviceClass:   'aForm-field-advice',
                validClass:    'aForm-field-valid',
                validateUrl:   'validate',
                valid: function(el){ /*if (window.console) console.log('Called valid', el)*/ },
                invalid: function(el){ /*if (window.console) console.log('Called invalid', el)*/ },
                clean: function(el){ /*if (window.console) console.log('Called clean', el) */ },
            };

            //Make field valid
            var makeValid = function(jNode) {
                jNode.addClass(options.validClass);
                options.valid(jNode);
            };
            //Make field invalid
            var makeInvalid = function(jNode, message) {
                 jNode.addClass(options.invalidClass)
                      .parent().append('<div class="'+options.adviceClass+'">'+message+'</div>');
                 
                 options.invalid(jNode);
            };
            
            //Make field clean
            var makeClean = function(jNode) {
                jNode.removeClass(options.invalidClass+' '+options.validClass)
                     .parent().children('div.'+options.adviceClass).remove();
                     
                options.clean(jNode);
            };
            
            //validate field by ajax
            var validateAjax = function(jForm, jNode) {
                var data = {'validateField':jNode.attr('name')};
                
                var formData = jForm.serializeArray();
                for (i in formData) {
                    data[formData[i].name] = formData[i].value;
                }

                $.post(options.validateUrl,
                   data,
                   function (data) {
                       //makeClean(jNode)
                       if (data.success == true) {
                           makeValid(jNode);
                       } else {
                           makeInvalid(jNode, data.message);
                       }
                   }, 
                   "json"
                );
            };
            
            var init = function(jForm) {

                jForm.find('.' + options.requiredClass + ', .' + options.validateClass).unbind().blur(function() {
                    var _jNode = $(this);
                    if ((!_jNode.hasClass(options.validClass) && !_jNode.hasClass(options.invalidClass))
                        || _jNode.data('value') != this.value) {
                        if (_jNode.hasClass(options.requiredClass)) {
                            makeClean(_jNode);
                            if (this.value.length < 1) {
                                makeInvalid(_jNode, this.title || options.requiredMessage);
                            } else {
                                if (_jNode.hasClass(options.validateClass) == false) {
                                    makeValid(_jNode);
                                }
                            }
                        }
                        if (_jNode.hasClass(options.validateClass)) {
                            if (this.value.length > 0) {
                                validateAjax(jForm, $(this));
                            }
                        }
                        _jNode.data('value', this.value);
                    }
                }).change(function() {
                    makeClean($(this));
                });
                
                jForm.find('input:submit').click(function(e){
                    if ($(this).data('aForm')) {
                        return;
                    } else {
                        $(this).data('aForm', true);
                    }
                    e.preventDefault();
                    
                    var ok = true;
                    
                    jForm.find('.'+options.requiredClass).each(function() {
                        var _jNode = $(this);
                        
                        if ((_jNode.hasClass(options.validClass) == false) &&
                            (_jNode.hasClass(options.invalidClass) == false)) {
                            _jNode.blur();
                        }
                        
                        if (_jNode.hasClass(options.invalidClass) == true) {
                            ok = false;
                        }
                    });
                    jForm.find('.'+options.validateClass).each(function() {
                        if ($(this).hasClass(options.invalidClass) == true) {
                            ok = false;
                        }
                    });
                    if (ok == true) {
                        jForm.submit();
                    }
                });
            };
   
            var options =  $.extend(defaults, options);

            //run thought all passed elements
            return this.each(function() {
                var jForm = $(this);
                
                if (!jForm.is('form')) {
                    throw this.tagName + " is not a form";
                }
                if (!jForm.data('aForm')) {
                    jForm.data('aForm', options);
                }
                options = jForm.data('aForm');
                
                init(jForm);
            });
        }
    });
})(jQuery);