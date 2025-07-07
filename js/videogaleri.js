/*
🎬 Video playlist UI Design like Skillshare With Vanilla JavaScript
👨🏻‍⚕️ By: Coding Design

You can do whatever you want with the code. However if you love my content, you can subscribed my YouTube Channel
🌎link: www.youtube.com/codingdesign
*/

const main_video = document.querySelector('.main-video video');
const main_video_title = document.querySelector('.main-video .title');
const video_playlist = document.querySelector('.video-playlist .videos');

let data = [
    {
        'id': 'a1',
        'title': 'Intro MAN 1 Pontianak by Fiddy',
        'name': 'profilbyfiddy.mp4',
        'duration': '1:24',
    },
    {
        'id': 'a2',
        'title': 'RECTOVERSO 45 - VIDEO ANGKATAN 45 MAN 1 PONTIANAK',
        'name': 'RECTOVERSO 45 - VIDEO ANGKATAN 45 MAN 1 PONTIANAK.mp4',
        'duration': '5:14',
    },
    {
        'id': 'a3',
        'title': 'Sekolah Inspiring-Aksi OSIS MANSA bersama BMI Pontianak',
        'name': 'Sekolah Inspiring-Aksi OSIS MANSA bersama BMI Pontianak.mp4',
        'duration': '4:02',
    },

    {
        'id': 'a4',
        'title': 'Rehabilitasi & Renovasi MAN 1 Pontianak - Kota Pontianak Kalimantan Barat',
        'name': 'Rehabilitasi & Renovasi MAN 1 Pontianak - Kota Pontianak Kalimantan Barat.mp4',
        'duration': '4:03',
    },

];

data.forEach((video, i) => {
    let video_element = `
                <div class="video" data-id="${video.id}">
                    <img src="img/play.svg" alt="">
                    <p class="title">${i + 1 > 9 ? i + 1 : '0' + (i + 1)}. </p>
                    <marquee><p class="title">${video.title} <span class="title-time">${video.duration}</span></p>
                </div>
    `;
    video_playlist.innerHTML += video_element;
})

let videos = document.querySelectorAll('.video');
videos[0].classList.add('active');
videos[0].querySelector('img').src = 'img/pause.svg';

videos.forEach(selected_video => {
    selected_video.onclick = () => {

        for (all_videos of videos) {
            all_videos.classList.remove('active');
            all_videos.querySelector('img').src = 'img/play.svg';

        }

        selected_video.classList.add('active');
        selected_video.querySelector('img').src = 'img/pause.svg';

        let match_video = data.find(video => video.id == selected_video.dataset.id);
        main_video.src = 'gal/videos/' + match_video.name;
        main_video_title.innerHTML = match_video.title;
    }
});
