$(function() {
    submitComment();
    hashValue();
    onFocusValidation();
    let $parentCommentId = '';
    replyComment();
});

function replyComment() {
    $(document).on("click", '.comment-btn.reply', function(event) {
        const parentCommentId = $(this).parent().attr('id');
        $('#' + parentCommentId + ' .comment-btn.reply').hide();
    });
}

// Scroll to paramlink
function hashValue() {
    const hash = window.location.hash;
    if (hash != '') {
        $('html, body').stop().animate({
            scrollTop: ($(hash).offset().top)
        }, 2000);
    }
}

// Submit form using ajax
function submitComment() {
    $(document).on('submit', '.tx_newscomments #comment-form', function(event) {
        event.preventDefault();
        const $form = $(this);
        const ajaxURL = $form.attr('action');
        const dataType = $('.tx_newscomments #dataType').val() || 'json';
        const $submitBtn = $('.tx_newscomments #submit');
        if (!validateField()) {
            return false;
        }
        $.ajax({
            type: 'POST',
            url: ajaxURL,
            dataType: dataType,
            data: $form.serialize(),
            beforeSend: function() {
                $submitBtn.prop('disabled', true).css('cursor', 'not-allowed');
            },
            success: function(response) {
            let responseData;
            try {
                responseData = typeof response === 'string' ? JSON.parse(response) : response;
            } catch (e) {
                console.error("Invalid JSON:", e);
                alert("Unexpected server response.");
                return;
            }

            $(".tx_newscomments #comments-list").load(location.href + " .tx_newscomments #comments-list>*", function(responseTxt, statusTxt, jqXHR) {
                if (statusTxt === "success") {
                    $.each(responseData, function(key, val) {
                        const $msgBox = val.parentId == 0
                            ? $('.tx_newscomments .thanksmsg')
                            : $('.tx_newscomments .thanksmsg-' + val.parentId);

                        $msgBox.show();
                        $('html, body').animate({
                            scrollTop: $msgBox.offset().top
                        }, 1000);

                        setTimeout(() => {
                            $msgBox.fadeOut("slow");
                        }, 5000);

                        if (val.parentId > 0) {
                            $('.tx_newscomments #comments-' + val.parentId).fadeIn('slow');
                            $('.tx_newscomments #parentId').val('');
                        }
                    });
                        $form[0].reset();
                        // 🔄 Reload page after 1 seconds
                        setTimeout(() => {
                            location.reload();
                    }, 1000);
                } else {
                    alert("Error loading comments list: " + jqXHR.status + " " + jqXHR.statusText);
                }
                });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX error:", textStatus, errorThrown);
                alert("Something went wrong: " + textStatus);
            },
            complete: function() {
                $submitBtn.prop('disabled', false).css('cursor', 'pointer');
            }
        });
    });

    $(document).on("click", ".reply", function(event) {
        const parentCommentId = $(this).parent().attr('id');
        $(this).siblings('.like').hide();

        const commentHTML = $('.active-comment-form').html();
        $('.active-comment-form .comment-form')[0].reset();
        $('.active-comment-form').html('');
        $('.active-comment-form').removeClass('active-comment-form');
        $(this).siblings('.reply-form').append(commentHTML);
        $('#comment-form-close-btn').show();
        removeDefaultValidation();
        $(this).siblings('.reply-form').addClass('active-comment-form');

        $('.active-comment-form #submit')
            .removeClass('rd-btn-send')
            .addClass('rd-btn-reply');
        $('.active-comment-form .text-muted').remove();
        $('.rd-comment-likes-count').remove();
        $('.comment-btn.show').remove();

        $('html, body').stop().animate({
            scrollTop: ($('.tx_newscomments #reply-form-' + parentCommentId).offset().top)
        }, 1000);

        $('.tx_newscomments #parentId').val(parentCommentId);
        onFocusValidation();
    });

    $(document).on('click', '#comment-form-close-btn', function () {
        $(this).closest('.reply-form').prev().show();
        $(this).closest('.reply-form').empty();
        $('.tx_newscomments #parentId').val('');
        closeReplyForm();
        $(this).closest('.rd-comment-likes-count').prev().show();
        $('.tx_newscomments #comment-form')[0]?.reset();
            // 🔄 Reload page after 1 seconds
            setTimeout(() => {
                location.reload();
        }, 50);
    });
}

// Show/Hide comment children
$(document).ready(function () {
    $(document).on("click", ".comment-btn.show", function () {
        const $button = $(this);
        const $parentComment = $button.closest("li");
        const $childReplies = $parentComment.children("ul.comments-list.reply-list");

        if ($childReplies.length) {
            const isVisible = $childReplies.first().is(":visible");

            $childReplies.toggle(); // Toggle direct child replies
            $button.text(isVisible ? "Show Comments" : "Hide Comments");
        }
    });
});



// like comment using Ajax
$(document).on('click', '.comment-btn.like', function(e) {
    e.preventDefault();

    // Grab the URL that includes `type=1730800496`
    let likeAjaxUrl = $('.comments-container').data('like-url');
    // Remove action param from URL if it's included
    likeAjaxUrl = likeAjaxUrl.replace(/([&?])tx_comments_comment\[action\]=[^&]*/g, '');

    const commentId = $(this).closest('.comment-footer').attr('id');
    const $icon     = $(this).find('i');
    const isLiked   = $icon.hasClass('rd-icon-liked');
    const action    = isLiked ? 'unlike' : 'like';
    console.log('→ Action:', action);

    // Toggle the icon immediately (optional UX improvement)
     $icon.toggleClass('rd-icon-liked rd-icon-like');

        $.ajax({
            url: likeAjaxUrl,
            method: 'POST',
            dataType: 'json',
            data: {
            'tx_comments_comment[commentId]': commentId,
            'tx_comments_comment[action]': 'like', // always call likeAction
            'tx_comments_comment[userAction]': action, // real intent: like or unlike
            'tx_comments_comment[controller]': 'Comment'
            },
            success: function(response) {
            if (response.success) {
                const message = action === 'like'
                ? '✅ You liked the comment!'
                : '❎ You unliked the comment!';
                alert(message);
                setTimeout(() => {
                    location.reload();
                }, 50);
            } else {
                alert('Failed to update like: ' + (response.error || 'Unknown error'));
                // If something went wrong, revert the icon
                $icon.toggleClass('rd-icon-liked rd-icon-like');
            }
            },
            error: function(jqXHR, textStatus, errorThrown) {
            // Revert the icon if it was toggled
            $icon.toggleClass('rd-icon-liked rd-icon-like');
            alert('AJAX error: ' + textStatus);
            }
        });
});


// Open form on close button click
function closeReplyForm() {
    const commentHTML = $('.active-comment-form').html();

    $('.tx_newscomments .active-comment-form').html('');
    $('.tx_newscomments .active-comment-form').removeClass('active-comment-form');
    $('.tx_newscomments #form-comment-view').html(commentHTML);
    $('.tx_newscomments #form-comment-view').addClass('active-comment-form');

    $('.tx_newscomments .comment-btn.reply').show();
    $('.tx_newscomments .comment-btn.like').show();
    $('.tx_newscomments #comment-form-close-btn').hide();

    $('#parentId').val('');
    removeDefaultValidation();
    onFocusValidation();
}

// Custom Validation 
function validateField() {
    let flag = 1;
    let elementObj;
    const terms = document.getElementsByName('tx_newscomments_newscomments[newComment][terms]').length;

    if (!$('.tx_newscomments #name').val()) {
        $(".tx_newscomments #name").parent().addClass('has-error');
        $('.tx_newscomments #name_error').show();
        flag = 0;
    } else {
        if (!validateName($('.tx_newscomments #name').val())) {
            $(".tx_newscomments #name_error_msg").show();
            $(".tx_newscomments #name_error").hide();
            $(".tx_newscomments #name").parent().addClass('has-error');
            flag = 0;
        } else {
            $(".tx_newscomments #name").parent().removeClass('has-error');
            $(".tx_newscomments #name_error_msg").hide();
            $(".tx_newscomments #name_error").hide();
        }
    }

    if (!$('.tx_newscomments #email').val()) {
        $(".tx_newscomments #email").parent().addClass('has-error');
        $(".tx_newscomments #email_error").show();
        $(".tx_newscomments #email_error_msg").hide();
        flag = 0;
    } else {
        if (!validateEmail($('.tx_newscomments #email').val())) {
            $(".tx_newscomments #email_error_msg").show();
            $(".tx_newscomments #email_error").hide();
            $(".tx_newscomments #email").parent().addClass('has-error');
            flag = 0;
        } else {
            $(".tx_newscomments #email").parent().removeClass('has-error');
        }
    }

    if (!$('.tx_newscomments #comment').val()) {
        $(".tx_newscomments #comment").parent().addClass('has-error');
        $(".tx_newscomments #comment_error").show();
        flag = 0;
    } else {
        const length = $.trim($(".tx_newscomments #comment").val()).length;
        if (length == 0) {
            $(".tx_newscomments #comment_error").show();
            $(".tx_newscomments #comment").parent().addClass('has-error');
            flag = 0;
        } else {
            $(".tx_newscomments #comment").parent().removeClass('has-error');
        }
    }

    if (terms) {
        if (!$('.tx_newscomments input[name="tx_newscomments_newscomments[newComment][terms]"]:checked').length) {
            $(".tx_newscomments #terms").closest('.rd-form-group').addClass('has-error');
            $(".tx_newscomments #terms_error").show();
            flag = 0;
        } else {
            $(".tx_newscomments #terms").closest('.rd-form-group').removeClass('has-error');
            $(".tx_newscomments #terms_error").hide();
        }
    }

    return flag === 1;
}

// Custom validation for onfocus
function onFocusValidation() {
    $(".tx_newscomments #name").focusout(function() {
        const elementObj = $(this);
        if (elementObj.val() != '') {
            if (!validateName($('.tx_newscomments #name').val())) {
                $(".tx_newscomments #name_error_msg").show();
                $(".tx_newscomments #name_error").hide();
                $(".tx_newscomments #name").parent().addClass('has-error');
            } else {
                elementObj.parent().removeClass('has-error');
                $(".tx_newscomments #name_error_msg").hide();
                $(".tx_newscomments #name_error").hide();
            }
        } else {
            $(".tx_newscomments #name").parent().addClass('has-error');
            $(".tx_newscomments #name_error").show();
            $(".tx_newscomments #name_error_msg").hide();
        }
    });

    $(".tx_newscomments #email").focusout(function() {
        const elementObj = $(this);
        if (elementObj.val() != '') {
            if (!validateEmail($('.tx_newscomments #email').val())) {
                $(".tx_newscomments #email_error_msg").show();
                $(".tx_newscomments #email_error").hide();
                $(".tx_newscomments #email").parent().addClass('has-error');
            } else {
                elementObj.parent().removeClass('has-error');
                $(".tx_newscomments #email_error_msg").hide();
                $(".tx_newscomments #email_error").hide();
            }
        } else {
            $(".tx_newscomments #email").parent().addClass('has-error');
            $(".tx_newscomments #email_error").show();
            $(".tx_newscomments #email_error_msg").hide();
        }
    });

    $(".tx_newscomments #comment").focusout(function() {
        const elementObj = $(this);
        if (elementObj.val() != '') {
            const length = $.trim($(".tx_newscomments #comment").val()).length;
            if (length == 0) {
                $(".tx_newscomments #comment_error").show();
                $(".tx_newscomments #comment").parent().addClass('has-error');
            } else {
                $(".tx_newscomments #comment").parent().removeClass('has-error');
                $(".tx_newscomments #comment_error").hide();
            }
        } else {
            $(".tx_newscomments #comment").parent().addClass('has-error');
            $(".tx_newscomments #comment_error").show();
        }
    });

    $('.tx_newscomments input[name="tx_newscomments_newscomments[newComment][terms]"]').on('change', function() {
        if (!$('.tx_newscomments input[name="tx_newscomments_newscomments[newComment][terms]"]:checked').length) {
            $(".tx_newscomments #terms").closest('.rd-form-group').addClass('has-error');
            $(".tx_newscomments #terms_error").show();
        } else {
            $(".tx_newscomments #terms").closest('.rd-form-group').removeClass('has-error');
            $(".tx_newscomments #terms_error").hide();
        }
    });
}

function removeDefaultValidation() {
    $(".tx_newscomments #name").parent().removeClass('has-error');
    $(".tx_newscomments #name_error").hide();
    $(".tx_newscomments #name_error_msg").hide();

    $(".tx_newscomments #email").parent().removeClass('has-error');
    $(".tx_newscomments #email_error").hide();
    $(".tx_newscomments #email_error_msg").hide();

    $(".tx_newscomments #comment").parent().removeClass('has-error');
    $(".tx_newscomments #comment_error").hide();
}

// Validate Email field
function validateEmail(email) {
    const emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailReg.test(email);
}

// Validate Name field
function validateName(name) {
    const nameReg = /^[\p{L}\p{M} \-']+$/u;
    return nameReg.test(name);
}
