$(function() {
    
    var _container = jQuery('div#comments ul')
        , SubmitForm
        , Comment
        , CommentForm
        , ReplyForm
        , LoginForm;

    /**
     * Submit form
     */
    SubmitForm = function(form, options) {
        if (!(this instanceof SubmitForm)) {
            return new SubmitForm(form, options);
        }
        
        this.form = form;
    }
    
    SubmitForm.prototype.init = function () {
        var self = this;
        
        this.form.submit(function(e) {
            e.preventDefault();
            
            self.submit();
        });
    }
    
    SubmitForm.prototype.startLoading = function () {
        this.submitButton = this.form.find('input[type="submit"], button[type="submit"]');
        
        this.submitButton.attr('disabled', true);
    }
    
    SubmitForm.prototype.stopLoading = function () {
        this.submitButton.attr('disabled', false);
    }
    
    /**
     * Implements comment rendering
     */
    Comment = function(options) {
        if (!(this instanceof Comment)) {
            return new Comment(options);
        }

        this.options = jQuery.extend(this.options, options);
    }

    /**
     * Construct and append comment element
     */
    Comment.prototype.append = function(commentData) {

        var _listElement
            , _article;

        _listElement = jQuery('<li>', {
            'class' : 'comment',
            'id' : 'li-comment-' + commentData.id
        });

        _article = jQuery('<article>', {
            'id' :  'comment-' + commentData.id
        });

        _article.append(
            commentData.author,
            jQuery('<span>', {
                'class': 'says',
                'html': 'said:'
            }),
            jQuery('<p>', {
                'html': commentData.text
            })
        );

        _listElement.append(_article).appendTo(_container);
    };

    /**
     * Comment form manager
     */
    CommentForm = function(form, options) {
        if (!(this instanceof CommentForm)) {
            return new CommentForm(form, options);
        }

        jQuery.extend(this, new SubmitForm(form, options));

        // mixin options
        this.options = jQuery.extend(this.options, options);

        this.init();

        return this;
    }

    /**
     * Simple validation and posting comment
     */
    CommentForm.prototype.submit = function(e) {
        jQuery.when(this.add()).then(function(response) {
            console.log('r', response);
        });
        
    }
    
    CommentForm.prototype.add = function(e) {
        var dfrd = new jQuery.Deferred()
            , self = this;
        
        this.startLoading();
        
        setTimeout(function() {
            self.stopLoading();
            
            dfrd.resolve(1);
        }, 2000);
        
        return dfrd.promise();
    }
    
    LoginForm = function(form, options) {
        if (!(typeof this !== 'LoginForm')) {
            return new LoginForm(form, options);
        }
        
        jQuery.extend(this, new SubmitForm(form, options));
        
        // mixin options
        this.options = jQuery.extend(this.options, options);
        
        this.init();
    }
    
    LoginForm.prototype.submit = function(e) {
        jQuery.when(this.signIn()).then(function() {
            console.log('submit!');
        });
    }
    
    LoginForm.prototype.signIn = function() {
        var dfrd = new jQuery.Deferred()
            , self = this;
        
        this.startLoading();
        
        setTimeout(function() {
            self.stopLoading();
            
            dfrd.resolve(1);
        }, 2000);
        
        return dfrd.promise();
    }
    
    ReplyForm = function(form, options) {
        if (!(typeof this !== 'ReplyForm')) {
            return new ReplyForm(form, options);
        }
        
        jQuery.extend(this, new SubmitForm(form, options));
        
        // mixin options
        this.options = jQuery.extend(this.options, options);
        
        this.init();
    }

    /**
     * Prepare reply form
     */
    function prepareReply(element) {
        console.log('e', element, jQuery(element).attr('data-commentId'));
    }

    jQuery('div#comments').find('.reply').click(function(e) {
        e.preventDefault();
        
        prepareReply(this);
    });

    /**** Main "constructor" ****/

//    var form = new CommentForm(
//        jQuery('.comment > form').first()
//    );  
        
//    new LoginForm(
//        jQuery('#userLoginForm')
//    );
        
    
});