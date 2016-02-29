//
// Use internal $.serializeArray to get list of form elements which is
// consistent with $.serialize
//
// From version 2.0.0, $.serializeObject will stop converting [name] values
// to camelCase format. This is *consistent* with other serialize methods:
//
//   - $.serialize
//   - $.serializeArray
//
// If you require camel casing, you can either download version 1.0.4 or map
// them yourself.
//

(function($) {
    $.fn.serializeObject = function () {
        "use strict";

        var result = {};
        var extend = function (i, element) {
            var node = result[element.name];

            // If node with same name exists already, need to convert it to an array as it
            // is a multi-value field (i.e., checkboxes)

            if ('undefined' !== typeof node && node !== null) {
                if ($.isArray(node)) {
                    node.push(element.value);
                } else {
                    result[element.name] = [node, element.value];
                }
            } else {
                result[element.name] = element.value;
            }
        };

        $.each(this.serializeArray(), extend);
        return result;
    };
})(jQuery);


jQuery(document).ready(function($) {

    // remove menu item not show
    $('.js-remove-nav').remove();

    __init();

    // force reset password form
    if ( WP_Users.current_action == 'rp' ) {
        $('.wpu-modal').addClass('is-visible');
        $('body').trigger('wp_users_before_open');
        $('body').trigger('login_selected');
        $('#wpu-login').removeClass('is-selected');
        $('#st-change-password').addClass('is-selected');
    }

    $('.wpu-wrapper').each(function() {
        var w = $(this);
        if ( w.data('ajax-load') !== true ) {
            return ;
        }

        var _act = w.data('action');
        var data = { action :'wp_users_ajax', 'act' : _act, 'current_url' : WP_Users.current_url  };
        $.ajax({
            data: data,
            url: WP_Users.ajax_url,
            type: 'GET',
            success: function( html ) {
                html = $( html );
                w.html( html );
                __init( html  );
                $( "body").trigger( "wp_users_content_loaded", [ html ] );
            }
        });
    });


    // load singup modal

    $('.st-singup-btn, .wpu-login-btn').click( function( event ) {
        var target = $( event.target );
        var is_login = target.is('.wpu-login-btn');

        if ( is_login  ) {
            if ( target.data('is-logged') ) {
                return true;
            }
        }

        if ($('.wpu-modal').length > 0 ) {
            $('.wpu-modal').addClass('is-visible');
            $('body').trigger('wp_users_before_open');
            if ( is_login ) {
                $('body').trigger('login_selected');
            } else {
                $('body').trigger('signup_selected');
            }

        } else {
            var data = { action :'wp_users_ajax', 'act' : 'modal-template' };
            $.ajax({
                data: data,
                url: WP_Users.ajax_url,
                type: 'GET',
                success: function( html ) {
                    html = $( html );
                    $('body').append( html );
                    __init( html );
                    $('body').trigger('wp_users_before_open');
                    $('.wpu-modal').addClass('is-visible');
                    if ( is_login ) {
                        $('body').trigger('login_selected');
                    } else {
                        $('body').trigger('signup_selected');
                    }
                }
            });
        }

        return false;
    } );


	function __init( w ) {
        if ( typeof w === 'undefined' ) {
            w = $('body');
        }
        var $form_modal = ( $('.wpu-modal' , w).not('.st-loaded').length >0 )  ?  $('.wpu-modal' , w).not('.st-loaded') :  $('.wpu-modal').not('.st-loaded'),
            $form_login = $form_modal.find('#wpu-login'),
            $form_signup = $form_modal.find('#st-signup'  ),
            $form_forgot_password = $form_modal.find('#st-reset-password'),
            $form_change_password = $form_modal.find('#st-change-password'),
            $login_link = $form_modal.find( '.wpu-login-link' ),
            $signup_link = $form_modal.find( '.st-register-link' ),
            $forgot_password_link = $form_login.find('.st-lost-pwd-link'),
            $back_to_login_link = $form_modal.find('.st-back-to-login');

        $form_modal.addClass('st-loaded');

        $('body').on('wp_users_before_open', function() {
            hide_all_errors();
        });
        $('body').on('signup_selected', function() {
            signup_selected();
        });
        $('body').on('login_selected', function() {
            login_selected();
        });
        $('body').on('forgot_password_selected', function() {
            forgot_password_selected();
        });


        //close modal
        $('.wpu-modal').on('click', function(event) {
            if ( $form_modal.hasClass('st-disabled') ) {
                return false;
            }
            if ( $(event.target).is($form_modal) || $(event.target).is('.st-close-form') ) {
                $form_modal.removeClass('is-visible');
            }
        });
        //close modal when clicking the esc keyboard button
        $(document).keyup(function(event) {
            if ( $form_modal.hasClass('st-disabled') ) {
                return false;
            }
            if (event.which=='27') {
                $form_modal.removeClass('is-visible');
            }
        });

        $signup_link.on( 'click', function() {
            if ( $form_modal.hasClass('st-disabled') ) {
                return false;
            }
            signup_selected();
            return false;
        } );

        $login_link.on( 'click', function() {
            if ( $form_modal.hasClass('st-disabled') ) {
                return false;
            }
            login_selected();
            return false;
        } );

        //hide or show password
        $('.fieldset .hide-password' , w ).on('click', function() {
            var $this= $(this),
                p= $this.parent(),
                $password_field = $('input', p );

            if ( 'password' == $password_field.attr('type') ) {
                $password_field.attr('type', 'text');
                $this.text( WP_Users.hide_txt );
            } else {
                $password_field.attr('type', 'password');
                $this.text( WP_Users.show_txt );
            }
            //focus and move cursor to the end of input field
            $password_field.putCursorAtEnd();
            return false;
        });

        //show forgot-password form
        $forgot_password_link.on('click', function(event) {
            event.preventDefault();
            forgot_password_selected();
            return false;
        });

        //back to login from the forgot-password form
        $back_to_login_link.on('click', function(event) {
            login_selected();
            return false;
        });

        function login_selected() {
            $form_login.addClass('is-selected');
            $form_signup.removeClass('is-selected');
            $form_forgot_password.removeClass('is-selected');
            $form_change_password.removeClass('is-selected');
            $login_link.addClass('selected');
            $signup_link.removeClass('selected');
        }

        function signup_selected() {
            $form_login.removeClass('is-selected');
            $form_signup.addClass('is-selected');
            $form_forgot_password.removeClass('is-selected');
            $form_change_password.removeClass('is-selected');
            $login_link.removeClass('selected');
            $signup_link.addClass('selected');
        }

        function forgot_password_selected() {
            $form_login.removeClass('is-selected');
            $form_signup.removeClass('is-selected');
            $form_change_password.removeClass('is-selected');
            $form_forgot_password.addClass('is-selected');
        }

        function hide_all_errors() {
            // hide all errors fields when load
            $('.wpu-form .fieldset' ).click( function( ) {
                var p = $(this);
                p.find('input').removeClass('has-error');
                p.find('span').removeClass('is-visible');
            });
        }

        // hide error of input field
        $('.wpu-form .fieldset input', w ).click( function( ) {
            var p = $(this).parents('.fieldset');
            $(this).removeClass('has-error');
            p.find('span').removeClass('is-visible');
        });


        /**
         * Login form submit
         */
        $('.wpu-login-form', w ).submit( function() {
            //return false;
            var form = $(this);
            var formData = form.serializeObject();
            formData.action = 'wp_users_ajax';
            formData.act = 'do_login';
            form.addClass( 'st-loading loading' );
            $( '.st-error-message', form ).removeClass( 'is-visible' );
            $( '.has-error', form ).removeClass( 'has-error' );

            $.ajax({
                url: WP_Users.ajax_url,
                data: formData,
                type: 'POST',
                success: function( response ) {
                    form.removeClass( 'st-loading loading' );
                    if ( response === 'logged_success' ) {
                        var redirect_url = ( typeof formData.st_redirect_to !== undefined  & formData.st_redirect_to != '' ) ? formData.st_redirect_to : document.location.toString();
                        window.location = redirect_url;
                        return ;
                    } else {
                        try {
                            var res = JSON.parse( response);
                            $.each( res, function ( k , msg ) {
                                if ( $( '.'+k , form).length > 0 ) {
                                    var p = $( '.'+k , form);
                                    if ( $('.st-error-message', p ).length <= 0 ) {
                                        p.append( '<span class="st-error-message"></span>' );
                                    }
                                    $('.st-error-message', p).html( msg).addClass( 'is-visible' );
                                    p.find('input').toggleClass('has-error');
                                }
                            } );
                        } catch ( e ) {

                        }

                    }
                }
            });
            return false;
        } );

        // Back to login Link
        if ( $('.st-register-form' , w ).hasClass('in-st-modal') ) {
            $('.wpu-login-link', w ).click(function() {
                login_selected();
                return false;
            });
        }

        /**
         * Register form submit
         */
        $('.st-register-form' , w ).submit( function() {

            var form = $( this );
            var formData = form.serializeObject();
            formData.action = 'wp_users_ajax';
            formData.act = 'do_register';

            if ( $('input[name="st_accept_terms"]:checked', form ).length == 0  ) {
                $('.accept-terms .st-error-message' , form ).toggleClass('is-visible');
            }

            var submit_btn =  $('.st-submit', form);
            var txt = submit_btn.val();
            submit_btn.data('default-text', txt );
            if ( submit_btn.data('loading-text') !== '' ) {
                submit_btn.val( submit_btn.data('loading-text') ) ;
                submit_btn.attr('disabled', 'disabled');
            }

            form.addClass( 'st-loading loading' );
            $( '.st-error-message', form ).removeClass( 'is-visible' );
            $( '.has-error', form ).removeClass( 'has-error' );

            $.ajax({
                url: WP_Users.ajax_url,
                data: formData,
                type: 'POST',
                success: function( response ) {

                    form.removeClass( 'st-loading loading' );

                    submit_btn.val( submit_btn.data('default-text') ) ;
                    submit_btn.removeAttr('disabled');

                    if ( ! isNaN( response ) ) { // success - user created.

                        var redirect_url = ( typeof formData.st_redirect_to !== 'undefined'  & formData.st_redirect_to != '' ) ? formData.st_redirect_to : window.location;
                        $('input[type=text], input[type=email], input[type=password], input[type=number]',form).val('');
                        $('input[type=checkbox]',form).removeAttr('checked');
                        $('.wpu-msg',form).show(0);

                        return ;
                    } else {

                        try {
                            var res = JSON.parse( response);
                            $.each( res, function ( k , msg ) {
                                if ( $( '.'+k , form).length > 0 ) {
                                    var p = $( '.'+k , form);
                                    if ( $('.st-error-message', p ).length <= 0 ) {
                                        p.append( '<span class="st-error-message"></span>' );
                                    }
                                    $('.st-error-message', p).html( msg ).addClass( 'is-visible' );
                                    p.find('input').toggleClass('has-error');
                                }
                            } );
                        } catch ( e ) {

                        }

                    }
                }
            });
            return false;
        } );

        // Lost pwd form submit
        $('.wpu-form-reset-password', w ).submit( function() {
            var form = $(this);
            var formData = form.serializeObject();
            formData.action = 'wp_users_ajax';
            formData.act = 'retrieve_password';

            var submit_btn =  $('.st-submit', form);
            var txt = submit_btn.val();
            submit_btn.data('default-text', txt );
            if ( submit_btn.data('loading-text') !== '' ) {
                submit_btn.val( submit_btn.data('loading-text') ) ;
                submit_btn.attr('disabled', 'disabled');
            }

            form.addClass( 'st-loading loading' );
            $( '.st-error-message', form ).removeClass( 'is-visible' );
            $( '.has-error', form ).removeClass( 'has-error' );

            $.ajax({
                url: WP_Users.ajax_url,
                data: formData,
                type: 'POST',
                success: function( response ) {
                    form.removeClass( 'st-loading loading' );
                    submit_btn.val( submit_btn.data('default-text') ) ;
                    submit_btn.removeAttr('disabled');
                    if ( response == 'sent' ) {
                        $( 'input[type=text], input[type=email], input[type=password], input[type=number]',form).val('');
                        $( 'input[type=checkbox]',form ).removeAttr('checked');
                        $( '.wpu-msg', form ).show(100);
                    } else {

                        try {
                            var res = JSON.parse( response);
                            $.each( res, function ( k , msg ) {
                                if ( $( '.'+k , form).length > 0 ) {
                                    var p = $( '.'+k , form);
                                    if ( $('.st-error-message', p ).length <= 0 ) {
                                        p.append( '<span class="st-error-message"></span>' );
                                    }
                                    $('.st-error-message', p).html( msg ).addClass( 'is-visible' );
                                    p.find('input').toggleClass('has-error');
                                }
                            } );
                        } catch ( e ) {
                            //console.log('catch e');
                            //console.log(response);
                            $('.wpu-msg', form ).addClass('has-error').html( response).show();
                        }
                    }
                }
            });

            return false;
        } );


        // change pwd form submit
        $('.wpu-form-change-password', w).submit( function() {

            var form = $(this);
            var formData = form.serializeObject();
            formData.action = 'wp_users_ajax';
            formData.act = 'do_reset_pass';

            var submit_btn =  $('.st-submit', form);
            var txt = submit_btn.val();
            submit_btn.data('default-text', txt );
            if ( submit_btn.data('loading-text') !== '' ) {
                submit_btn.val( submit_btn.data('loading-text') ) ;
                submit_btn.attr('disabled', 'disabled');
            }

            $('.wpu-msg', form).hide(1);
            form.addClass( 'st-loading loading' );
            $( '.has-error', form ).removeClass( 'has-error' );

            $.ajax({
                url: WP_Users.ajax_url,
                data: formData,
                type: 'POST',
                success: function( response ) {
                    form.removeClass( 'st-loading loading' );
                    submit_btn.val( submit_btn.data('default-text') ) ;
                    submit_btn.removeAttr('disabled');

                    if ( response == 'changed' ) {
                        $('input[type=text], input[type=email], input[type=password], input[type=number]',form).val('');
                        $('.wpu-msg', form).show(1);
                    } else {

                        try {
                            var res = JSON.parse( response );
                            $('.wpu-msg', form).hide(1);
                            if ( typeof res.error !== 'undefined'  && res.error !=='' ) {
                                $('.st-errors-msg', form).html(res.error).show(1);
                            }
                            $.each( res, function ( key, value ) {
                                var  p = $('.' + key, form );
                                $('.st-error-message', p).html( value ).toggleClass('is-visible');
                                p.find('.input').toggleClass('has-error');
                            } );
                        } catch ( e ){

                        }

                    }
                }
            });

            return false;
        } );

        // Profile Submit
        $( 'form.wpu-form-profile', w ).submit( function() {

            var form = $(this);
            var formData = form.serializeObject();
            formData.action = 'wp_users_ajax';
            formData.act = 'do_update_profile';

            var submit_btn =  $('.st-submit', form);
            var txt = submit_btn.val();
            submit_btn.data('default-text', txt );
            if ( submit_btn.data('loading-text') !== '' ) {
                submit_btn.val( submit_btn.data('loading-text') ) ;
                submit_btn.attr('disabled', 'disabled');
            }

            $('.wpu-msg', form).hide(1);

            form.addClass( 'st-loading loading' );

            $.ajax({
                url: WP_Users.ajax_url,
                data: formData,
                type: 'POST',
                success: function( response ) {

                    form.removeClass( 'st-loading loading' );

                    submit_btn.val( submit_btn.data('default-text') ) ;
                    submit_btn.removeAttr('disabled');

                    if ( response == 'updated' ) {
                        var c_url = window.location.href;
                        if ( c_url.indexOf("st_profile_updated") == -1 ) {
                            if (c_url.indexOf("?") !== -1) {
                                c_url += '&st_profile_updated=1';
                            } else {
                                c_url += '?st_profile_updated=1';
                            }
                        }

                        // refresh page
                        window.location =  c_url;
                        $('.wpu-msg', form).show(1);
                    } else {
                        try {
                            var res = JSON.parse( response );
                            $('.wpu-msg', form).hide(1);
                            if ( typeof res.error !== 'undefined'  && res.error !=='' ) {
                                $('.st-errors-msg', form).html(res.error).show(1);
                            }
                            $.each( res, function ( key, value ) {
                                var  p = $('.' + key, form );
                                $('.st-error-message', p).html( value ).toggleClass('is-visible');
                                p.find('.input').toggleClass('has-error');
                            } );
                        } catch ( e ) {

                        }

                    }
                }
            });

            return  false;
        } );

        /**
         * Trigger when init
         */
        $( "body").trigger( "wp_users_init", [ w ] );

    }// end function init


    /// -------------------------------------------------


    function STCropPic( $cover,  settings ) {
        var that = this;
        var cid = 'cp-'+(  new Date().getTime() ),  upload_id = 'cpu-'+cid;

        if ( $( '.croppic-div', $cover).length <= 0 ){
            $cover.append( '<div class="croppic-div"></div>' );
        }

        that.cover = $cover;
        that.cropDiv = $( '.croppic-div', $cover);
        that.cropDiv.attr('id', cid );
        that.id = cid;
        that.uploadBtn = '';
        that.repositionBtn = '';
        that.settingsObj = '';
        that.img = $cover.data('cover') || '';
        that.loadImg = that.img;
        that.cropper = '';
        that.onAfterImgUploadCB = null;
        that.onAfterImgCropCBName = null;

        that.settings = $.extend({
            modal : false,
            type : 'avatar',
            zoom : false,
            uploadBtn : '',
            'button_text': 'Change',
            'remove_text': 'Remove',
            'upload_text':  'Upload',
        }, settings );

        that.init = function(){
            that.intSettings();

            if( that.settings.type !== 'avatar' ) {
                that.cover.css({ width: 'auto' });
                var width = that.cover.width();
                that.cover.width( width );

                $( window).resize( function() {
                    that.cover.css({ width: 'auto' });
                    var width = that.cover.width();
                    that.cover.width( width );
                } );
            }

        };

        that.intUpload = function(){
            that.initCroppic();
        };

        that.intSettings = function(){
            $( '.coppic-settings', that.cover ).remove();
            that.cover.append( that.settingsMarkup() );
            that.settingsObj = $( '.coppic-settings', that.cover );
            that.uploadBtn = $( '.cp-upload', that.settingsObj );
            that.uploadBtn.click( that.uploadBtnClick );
            that.repositionBtn = $( '.cp-reposition', that.settingsObj );
            that.repositionBtn.click( that.repositionBtnClick );
            $( '.cp-btn', that.settingsObj ).click( function(){
               $( '.cp-actions', that.settingsObj ).toggleClass('cp-active');
            });
            if ( that.loadImg == '' ) {
                $( '.cp-remove' , that.settingsObj).remove();
            }
            $( '.cp-remove' , that.settingsObj).click( that.removeImg );

            $(document).mouseup(function (e)
            {

                if (!that.settingsObj.is(e.target) // if the target of the click isn't the container...
                    && that.settingsObj.has(e.target).length === 0) // ... nor a descendant of the container
                {
                    $( '.cp-actions', that.settingsObj ).removeClass('cp-active');
                }
            });
        };

        that.removeImg =  function(){

            $.post( WP_Users.ajax_url, {
                _wpnonce: WP_Users._wpnonce,
                act: "remove_media",
                media_type: that.settings.type,
                action: "wp_users_ajax"
            }, function () {
                that.cover.css( { backgroundImage: '' } );
            } );

            $( '.cp-actions', that.settingsObj ).removeClass('cp-active');

            return false;
        };

        that.resetCoverImg = function(){
            that.cover.css( { backgroundImage: 'url('+that.loadImg+')' } );
        };

        that.uploadBtnClick = function(){
            //that.settings.hide();
            if (  typeof that.cropper === "object" ) {
                that.cropper.destroy();
            }
            that.loadImg = '';
            //that.onAfterImgUploadCBName = null;
            that.onAfterImgCropCBName = null;
            that.initCroppic();
            // open upload select file
            $( '.cropControlUpload', that.cover).click();
           // that.settingsObj.addClass('hide');
            return false;
        };

        that.repositionBtnClick =  function(){

            if (  typeof that.cropper === "object" ) {
                that.cropper.destroy();
            }
            if ( that.img === '' ) {
                return false;
            }
            that.settingsObj.addClass('hide');
            that.loadImg = that.img;
            //that.onAfterImgUploadCBName = null;
            that.onAfterImgCropCBName = that.onAfterImgCropCB;
            that.initCroppic();
            return false;
        };

        that.onAfterImgCropCB = function( ){
            that.loadImg  = $( '.croppedImg', that.cover ).attr('src');
            that.cropper.destroy();
            that.intSettings();
            that.resetCoverImg();
        };

        that.onAfterImgUploadCB =  function ( ){
            that.loadImg  = $( '.cropImgWrapper img', that.cover ).eq( 0 ).attr('src');
            if ( typeof that.loadImg !== "undefined" ) {
                //console.log( that.loadImg );
                that.cropper.destroy();
                that.intSettings();
                that.resetCoverImg();
            }

        };

        that.settingsMarkup = function() {
            var HTML =  '<div class="coppic-settings">' +
                            '<div class="cp-btn"><i class="cp-icon"></i><span>'+that.settings.button_text+'</span> </div>' +
                                '<div class="cp-actions"> ' +
                                '<div class="cp-upload" id="'+upload_id+'">'+that.settings.upload_text+'</div> ' +
                                // '<div class="cp-reposition">Reposition...</div> ' +
                                '<div class="cp-remove">'+that.settings.remove_text+'</div> ' +
                            '</div> ' +
                        '</div>';
            return  HTML;
        };

        that.initCroppic = function(){

            that.cropper = new Croppic( that.id, {
                //customUploadButtonId: that.uploadID,
                modal: that.settings.modal,

                uploadUrl: WP_Users.ajax_url,
                uploadData: {
                    _wpnonce: WP_Users._wpnonce,
                    act: "update_"+that.settings.type,
                    action: "wp_users_ajax"
                },
                cropUrl: WP_Users.ajax_url,
                cropData: {
                    _wpnonce: WP_Users._wpnonce,
                    act: "crop_"+that.settings.type,
                    action: "wp_users_ajax"
                },
                loadPicture: that.loadImg,
                zoomFactor: 40,
                imgEyecandy: false,
                doubleZoomControls: false,
                rotateFactor: 0, // 90
                rotateControls: false,
                scaleToFill: true,

                onBeforeImgUpload: function () {

                },
                onAfterImgUpload: that.onAfterImgUploadCB,
                onImgDrag: function () {

                },
                onImgZoom: function () {

                },
                onBeforeImgCrop: function () {

                },
                onAfterImgCrop: that.onAfterImgCropCBName ,
                onReset: function () {

                },
                onError: function (errormsg) {

                }
            });
        };

        that.init();

    }



    /* Profile croppic */

    if (  typeof Croppic !== 'undefined' ) {

        $( '.st-profile-cover').each( function(){
            var $cover = $( this);
            var is_can_change = $cover.data('change') ||  false;
            if ( is_can_change ) {
                new STCropPic($cover, {
                    modal: false,
                    zoom: false,
                    type: 'cover',
                    'button_text': WP_Users.cover_text,
                    'remove_text': WP_Users.remove_text,
                    'upload_txt': WP_Users.upload_text,
                });
            }
        } );

        $( '.st-profile-avatar').each( function(){
            var $cover = $( this);
            var is_can_change = $cover.data('change') ||  false;
            if ( is_can_change ) {
                new STCropPic( $cover, {
                    modal : false,
                    zoom : false,
                    type : 'avatar',
                    'button_text': WP_Users.avatar_text,
                    'remove_text': WP_Users.remove_text,
                    'upload_text':  WP_Users.upload_text,
                } );
            }

        } );

    }


});





//credits http://css-tricks.com/snippets/jquery/move-cursor-to-end-of-textarea-or-input/
jQuery.fn.putCursorAtEnd = function( ) {
	return this.each(function() {
    	// If this function exists...
    	if (this.setSelectionRange) {
      		// ... then use it (Doesn't work in IE)
      		// Double the length because Opera is inconsistent about whether a carriage return is one character or two. Sigh.
      		var len = jQuery(this).val().length * 2;
      		this.setSelectionRange(len, len);
    	} else {
    		// ... otherwise replace the contents with itself
    		// (Doesn't work in Google Chrome)
            jQuery(this).val(jQuery(this).val());
    	}
	});
};