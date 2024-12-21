<?php
// views/layout/footer.php
?>
    <script>
        // Video autoplay handling
        document.addEventListener('DOMContentLoaded', function() {
            const videos = document.querySelectorAll('.video-player video');
            
            // Intersection Observer for video autoplay
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.play();
                    } else {
                        entry.target.pause();
                    }
                });
            }, { threshold: 0.5 });
            
            videos.forEach(video => observer.observe(video));
            
            // Like button handling
            document.querySelectorAll('.action-btn.like').forEach(btn => {
                btn.addEventListener('click', function() {
                    const videoId = this.dataset.videoId;
                    fetch('/api/like', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ video_id: videoId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.classList.toggle('active');
                            const count = this.querySelector('span');
                            count.textContent = data.likes_count;
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>