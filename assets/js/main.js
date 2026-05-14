// assets/js/main.js

document.addEventListener('DOMContentLoaded', () => {
    const landing = document.getElementById('landing');
    const startBtn = document.getElementById('start-btn');
    const mainExp = document.querySelector('.main-experience');
    const video = document.getElementById('bg-video');
    const audio = document.getElementById('bg-audio');
    const heartBtn = document.getElementById('heart-btn');

    // Landing Trigger
    startBtn.addEventListener('click', () => {
        landing.style.opacity = '0';
        setTimeout(() => {
            landing.style.display = 'none';
            mainExp.style.display = 'flex';
            
            // Start media
            video.play().catch(e => console.log("Video play blocked:", e));
            if (audio) {
                audio.play().catch(e => console.log("Audio play blocked:", e));
            }
        }, 1000);
    });

    // Heart Interaction
    heartBtn.addEventListener('click', async () => {
        createHeartBurst();
        
        try {
            const response = await fetch('api/heart.php', { method: 'POST' });
            const data = await response.json();
            
            if (data.success) {
                // optional: update a counter if displayed
            }
        } catch (error) {
            console.error('Failed to heart:', error);
        }
    });

    function createHeartBurst() {
        const colors = ['#ff4757', '#ff6b81', '#ffafbd', '#fb6b6b'];
        for (let i = 0; i < 12; i++) {
            const heart = document.createElement('div');
            heart.className = 'floating-heart';
            heart.innerHTML = '❤️';
            heart.style.left = `calc(50% + ${Math.random() * 60 - 30}px)`;
            heart.style.bottom = '100px';
            heart.style.fontSize = `${Math.random() * 1.5 + 0.5}rem`;
            heart.style.setProperty('--tx', `${Math.random() * 200 - 100}px`);
            heart.style.setProperty('--ty', `-${Math.random() * 300 + 200}px`);
            heart.style.setProperty('--rot', `${Math.random() * 360}deg`);
            heart.style.animationDuration = `${Math.random() * 1 + 1}s`;
            
            mainExp.appendChild(heart);
            
            setTimeout(() => {
                heart.remove();
            }, 2000);
        }
    }
});
