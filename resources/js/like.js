export function initLikeButtons() {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (!csrfMeta) return;

    const csrf = csrfMeta.content;

    document.querySelectorAll('.js-like-btn').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const postId = btn.dataset.postId;
            const liked = btn.dataset.liked === '1';

            btn.disabled = true;

            const method = liked ? 'DELETE' : 'POST';

            try {
                const res = await fetch(`/posts/${postId}/like`, {
                    method,
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!res.ok) {
                    console.error('Like request failed', res.status);
                    return;
                }

                const data = await res.json();

                btn.dataset.liked = data.liked ? '1' : '0';
                btn.querySelector('.js-like-icon').textContent = data.liked ? '❤️' : '♡';
                btn.querySelector('.js-like-count').textContent = data.count;
            } catch (e) {
                console.error(e);
            } finally {
                btn.disabled = false;
            }
        });
    });
}
