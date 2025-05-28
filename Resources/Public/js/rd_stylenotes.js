document.addEventListener('DOMContentLoaded', function () {
    // Toggle news comments section
    document.querySelectorAll('.news-header').forEach(header => {
        const newsId = header.getAttribute('data-news-id');
        const commentsSection = document.getElementById(`comments-${newsId}`);
        const toggleIcon = document.getElementById(`toggle-icon-${newsId}`);

        if (!commentsSection || !toggleIcon) {
            console.warn(`Missing elements for news ID: ${newsId}`);
            return;
        }

        const toggleSection = () => {
            const isExpanded = header.getAttribute('aria-expanded') === 'true';
            header.setAttribute('aria-expanded', !isExpanded);
            commentsSection.style.display = isExpanded ? 'none' : 'block';
            commentsSection.classList.toggle('active', !isExpanded);
            toggleIcon.classList.toggle('active', !isExpanded);
            toggleIcon.textContent = isExpanded
                ? '+' // Use translation if available
                : '-';
        };

        header.addEventListener('click', toggleSection);
        header.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleSection();
            }
        });
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('toggle-replies')) {
            const commentId = e.target.getAttribute('data-comment-id');
            const repliesSection = document.getElementById(`replies-${commentId}`);
    
            if (!repliesSection) {
                console.warn(`Replies section not found for comment ID: ${commentId}`);
                return;
            }
    
            const isExpanded = repliesSection.classList.contains('active');
            repliesSection.style.display = isExpanded ? 'none' : 'block';
            repliesSection.classList.toggle('active', !isExpanded);
            const replyCount = e.target.textContent.match(/\((\d+)\)/)?.[1] || '0';
            e.target.textContent = isExpanded
                ? `Show Replies (${replyCount})`
                : `Hide Replies (${replyCount})`;
            e.target.setAttribute('aria-expanded', !isExpanded);
        }
    });
    
    document.querySelectorAll('.btn-danger').forEach(button => {
        button.addEventListener('click', (event) => {
            if (!confirm('Are you sure you want to delete this comment?')) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('.btn-pin').forEach(button => {
        button.addEventListener('click', (event) => {
            const action = button.classList.contains('pinned') ? 'unpin' : 'pin';
            if (!confirm(`Are you sure you want to ${action} this comment?`)) {
                event.preventDefault();
            }
        });
    });

    // Handle pinned comment styling
    document.querySelectorAll('.comment').forEach(comment => {
        const commentId = comment.getAttribute('data-comment-id');
        if (comment.classList.contains('pinned')) {
            const commentSection = document.querySelector(`.comment[data-comment-id="${commentId}"]`);
            if (commentSection) {
                commentSection.classList.add('pinned-comment');
            }
        }
    });
});