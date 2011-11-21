$(function() {
    
    var _container = jQuery('div#comments ul')
        , Comment
        , CommentForm;

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

        var _self = this;

        this.form = form;

        // mixin options
        this.options = jQuery.extend(this.options, options);

        // prevent default submit event and execute the self submit method
        form.submit(function(e) {
            e.preventDefault();

            _self.submit();
        });

        return this;
    }

    /**
     * Simple validation and posting comment
     */
    CommentForm.prototype.submit = function(e) {
        console.log('S!');
    }

    /**** Main "constructor" ****/

    var form = new CommentForm(
        jQuery('.comment form').first()
    );

    console.log(jQuery('.comment form'));
     
    
        
});