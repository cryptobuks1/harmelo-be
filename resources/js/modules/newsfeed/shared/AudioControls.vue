<template>
    <div id="audio-player-root">
        <!-- Hide the default audio player -->
        <div >
            <audio
                style="display:none"
                ref="player"
                :id="playerid"
            >
                <source :src="`${s3_site + post_details.track_details[0].track_path}`" type="audio/mpeg" /> <!--  -->
            </audio>
        </div>

        <!-- VIDEO BOX -->
        <div class="popup-picture-image-wrap video-box small"  @click="toggleAudio()" v-if="post_details != null"
            :style="[view_mode == 'share_mode' ? {'height': '400px !important'} : {}]">
            <!-- VIDEO BOX COVER -->
            <div class="video-box-cover popup-video-trigger" style="height: 100%; width: 100%;"> <!-- class="item-thumbnail"  -->
                <!-- VIDEO BOX COVER IMAGE class="video-box-cover-image liquid" -->
                <figure style="height: 100% !important; background-size: contain !important;">
                    <img :src="
                        post_details.track_details[0].image_path == '' ||
                        post_details.track_details[0].image_path == undefined ||
                        albumart == post_details.track_details[0].image_path ? defaultThumbnail : s3_site+post_details.track_details[0].image_path" alt="cover-04" style="object-fit: contain">
                </figure>
                <!-- /VIDEO BOX COVER IMAGE -->

                <!-- PLAY BUTTON -->
                <div class="play-button track__playBtn" :class="{'toggleBtns':isPlaying}">
                    <img id="rotating-play-btn" class="pausedBtn" :class="{'heart animated css':isPlaying}" :src="harmeloPlayer"
                        style="margin-left: 25px; position: absolute; top: -15px !important"
                        :style="[view_mode == 'preview_mode' ?
                            {'max-width':'167px', 'height':'167px'}
                            :
                            {'max-width':'96px !important', 'height':'96px !important'}]"
                    >

                    <svg class="play-button-icon icon-play playedBtn" style="margin-left: 35px; position: absolute; top: -15px !important"
                        :style="[view_mode == 'preview_mode' ?
                            {'width':'153px !important', 'height':'153px !important'}
                            :
                            {'width':'96px !important', 'height':'96px !important'}]"
                            @click="toggleAudio()">
                        <use xlink:href="#svg-play"></use>
                    </svg>

                    <!--<img id="rotating-play-btn" class="heart animated css pausedBtn" :src="harmeloPlayer" :height="view_mode == 'preview_mode' ? 150 : 84" v-show="isPlaying" >

                    <div>
                        <svg class="play-button-icon icon-play playedBtn" style=" margin-left: 30px;"
                            :style="[view_mode == 'preview_mode' ?
                                {'width':'124px !important', 'height':'124px !important'}
                                :
                                {'width':'74px !important', 'height':'74px !important'}]"
                            v-show="!isPlaying" @click="toggleAudio()">
                            <use xlink:href="#svg-play"></use>
                        </svg>
                    </div>-->

                </div>
                <!-- /PLAY BUTTON -->

                <!-- VIDEO BOX INFO -->
                <div class="video-box-info">

                    <input
                        v-model="playbackTime"
                        type="range"
                        min="0"
                        :max="audioDuration"
                        class="slider w-full h-full video-box-text"
                        id="position"
                        name="position"
                    />

                    <p class="video-box-title" style="position: absolute; bottom: 55px; font-size: 1rem">{{ post_details.user_details[0].name }} - {{ post_details.track_details[0].title }}</p>

                    <!-- VIDEO BOX TEXT -->
                    <p class="video-box-text" style="position: absolute; bottom: 35px" v-show="audioLoaded">{{ elapsedTime() }} - {{ totalTime() }} </p>
                    <p class="video-box-text" v-show="!audioLoaded" v-html="totalTime()">Loading please wait...</p>
                    <!-- /VIDEO BOX TEXT -->
                </div>
                <!-- /VIDEO BOX INFO -->
            </div>
            <!-- /VIDEO BOX COVER -->
        </div>
        <!-- /VIDEO BOX -->

    </div>
</template>

<script>
import moment from 'moment';
import constant from '../../../utilities/Constant';

import playerLogo from '../../../../assets/logo/harmelo-logo-no-word-white.png';
import defaultAlbumArt from '../../../../assets/img/harmelo_album_art.png';

//import { mapState } from 'vuex'
export default {
    props: ["url", "playerid", "albumart", 'post_details', 'view_mode'],
    /**
     * playbackTime = local var that syncs to audio.currentTime
     * audioDuration = duration of audio file in seconds
     * isPlaying = boolean (true if audio is playing)
     *
     **/
    data() {
        return {
            s3_site: constant.s3_site,
            moment: moment,
            const: constant,
            defaultThumbnail: defaultAlbumArt,
            harmeloPlayer: playerLogo,
            playbackTime: 0,
            audioDuration: 100,
            audioLoaded: false,
            isPlaying: false,
        };
    },
    methods: {
        _closeDialog() {
            this.$emit('close-details-modal');
        },
        //Set the range slider max value equal to audio duration
        initSlider() {
            var audio = this.$refs.player;
            if (audio) {
                this.audioDuration = Math.round(audio.duration);
            }
        },
        //Convert audio current time from seconds to min:sec display
        convertTime(seconds){
                            const format = val => `0${Math.floor(val)}`.slice(-2);
                var hours = seconds / 3600;
                var minutes = (seconds % 3600) / 60;
                return [minutes, seconds % 60].map(format).join(":");
        },
        //Show the total duration of audio file
        totalTime() {
            var audio = this.$refs.player;
            if (audio) {
                var seconds = audio.duration;
                return this.convertTime(seconds);
            } else {
                return '00:00';
            }
        },
        //Display the audio time elapsed so far
        elapsedTime() {
            var audio = this.$refs.player;
            if (audio) {
                var seconds = audio.currentTime;
                return this.convertTime(seconds);
            } else {
                return '00:00';
            }
        },
        //Playback listener function runs every 100ms while audio is playing
        playbackListener(e) {
            var audio = this.$refs.player;
            //Sync local 'playbackTime' var to audio.currentTime and update global state
            this.playbackTime = audio.currentTime;

            //console.log("update: " + audio.currentTime);
            //Add listeners for audio pause and audio end events
            audio.addEventListener("ended", this.endListener);
            audio.addEventListener("pause", this.pauseListener);
        },
        //Function to run when audio is paused by user
        pauseListener() {
            this.isPlaying = false;
            this.listenerActive = false;
            this.cleanupListeners();
        },
        //Function to run when audio play reaches the end of file
        endListener() {
            this.isPlaying = false;
            this.listenerActive = false;
            this.cleanupListeners();
        },
        //Remove listeners after audio play stops
        cleanupListeners() {
            var audio = this.$refs.player;
            audio.removeEventListener("timeupdate", this.playbackListener);
            audio.removeEventListener("ended", this.endListener);
            audio.removeEventListener("pause", this.pauseListener);
            //console.log("All cleaned up!");
        },
        toggleAudio() {
            var audio = this.$refs.player;
            //var audio = document.getElementById("audio-player");
            if (audio.paused) {
                audio.play();
                this.isPlaying = true;
            } else {
                audio.pause();
                this.isPlaying = false;
            }
        },
    },
    mounted: function() {
      // nextTick code will run only after the entire view has been rendered


      this.$nextTick(function() {
        var audio=this.$refs.player;

        //Wait for audio to load, then run initSlider() to get audio duration and set the max value of our slider
        // "loademetadata" Event https://www.w3schools.com/tags/av_event_loadedmetadata.asp
        audio.addEventListener(
          "loadedmetadata",
          function(e) {
            this.initSlider();
          }.bind(this)
        );
        // "canplay" HTML Event lets us know audio is ready for play https://www.w3schools.com/tags/av_event_canplay.asp
        audio.addEventListener(
          "canplay",
          function(e) {
            this.audioLoaded=true;
          }.bind(this)
        );
        //Wait for audio to begin play, then start playback listener function
        this.$watch("isPlaying",function() {
          if(this.isPlaying) {
            var audio=this.$refs.player;
            this.initSlider();
            //console.log("Audio playback started.");
            //prevent starting multiple listeners at the same time
            if(!this.listenerActive) {
              this.listenerActive=true;
              //for a more consistent timeupdate, include freqtimeupdate.js and replace both instances of 'timeupdate' with 'freqtimeupdate'
              audio.addEventListener("timeupdate",this.playbackListener);
            }
          }
        });
        //Update current audio position when user drags progress slider
        this.$watch("playbackTime",function() {
            var audio=this.$refs.player;
        var diff=Math.abs(this.playbackTime-this.$refs.player.currentTime);

          //Throttle synchronization to prevent infinite loop between playback listener and this watcher
          if(diff>0.01) {
            this.$refs.player.currentTime=this.playbackTime;
          }
        });
      });
    }
};
</script>

<style scoped>
.pausedBtn {
    opacity: 0;
    transition: opacity 0.6s
}
.toggleBtns .pausedBtn {
    opacity: 1;
    transition: opacity 0.6s
}
.toggleBtns .playedBtn {
    opacity: 0;
    transition: opacity 0.6s
}
.playedBtn {
    opacity: 1;
    transition: opacity 0.6s
}
@media (max-width: 1024px) {
    .pausedBtn {
            max-width: 95px !important;
            height: 95px !important;
            margin-left: 34px !important;
            position: absolute;
            top: -37px;
    }
}
@media (max-width: 1024px) {
    .playedBtn {
        max-width: 77px !important;
        height: 77px !important;
        margin-left: 48px !important;
        position: absolute !important;
        top: -29px !important;
    }
}
/* Play/Pause Button */
.play-button{
    height: 45px
}
input[type="range"] {
    margin: auto;
    -webkit-appearance: none;
    position: relative;
    overflow: hidden;
    width: 100%;
    cursor: pointer;
    outline: none;
    border-radius: 0; /* iOS */
    background: transparent;
}
input[type="range"]:focus {
    outline: none;
}
::-webkit-slider-runnable-track { /* this is the non played line */
    background: #a7a4a459;
}
/*
 * 1. Set to 0 width and remove border for a slider without a thumb
 */
::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 40px; /* 1 */
    height: 40px;
    background: #fff;
    box-shadow: -100vw 0 0 100vw #fff; /*cec1fa */
    /*border: none;  2px solid #999; */
    border-radius: 50% !important;
}
::-moz-range-track {
    height: 40px;
    background: #a7a4a4;
}
::-moz-range-thumb {
    background: #fff;
    height: 40px;
    width: 40px; /* 20px; */
    border: none; /* 3px solid #999; */
    border-radius: 50% !important;
    box-shadow: -100vw 0 0 100vw dodgerblue;
    box-sizing: border-box;
}
::-ms-fill-lower {
    background: dodgerblue;
}
::-ms-thumb {
    background: #fff;
    border: 2px solid #999;
    height: 40px;
    width: 20px;
    box-sizing: border-box;
}
::-ms-ticks-after {
    display: none;
}
::-ms-ticks-before {
    display: none;
}
::-ms-track {
    background: #a7a4a4;
    color: transparent;
    height: 40px;
    border: none;
}
::-ms-tooltip {
    display: none;
}
/**
* OVERRIDES
*/
.video-box.small .video-box-cover .video-box-cover-image {
    border-top-left-radius: 0px;
    border-bottom-left-radius: 0px !important;
    border-bottom-right-radius: 12px !important;
    border-top-right-radius: 12px !important;
}
.video-box .video-box-cover .video-box-cover-image {
    border-bottom-right-radius: 12px !important;
    border-top-right-radius: 12px !important;
}
.video-box.small .video-box-cover:after {
    border-radius: 0 !important;
}

/* AUDIO PROGRESS BAR */
input[type=range] {
    -webkit-appearance: none;
    -moz-apperance: none;
    height: 3px;
    border-radius: 33px;
    position: absolute;
    bottom: 0%;
    width: 100%;
    left: 0;

    background-image: -webkit-gradient(linear,
        left top,
        right top,
        color-stop(15%, #3f249c),
        color-stop(15%, #F5D0CC));

    background-image: -moz-linear-gradient(left center,
        #3f249c 0%, #3f249c 15%,
        #F5D0CC 15%, #F5D0CC 100%);
}

input[type="range"]::-moz-range-track {
    border: none;
    background: none;
    outline: none;
}

input[type=range]:focus {
    outline: none;
    border: none;
}

input[type=range]::-webkit-slider-thumb {
    -webkit-appearance: none !important;
    /*background-color: #3f249c;
    height: 13px;
    width: 13px;
    border-radius: 50%;*/
}

input[type=range]::-moz-range-thumb {
    -moz-appearance: none !important;
    /*background-color: #3f249c;
    border: none;
    height: 13px;
    width: 13px;
    border-radius: 50%;*/
}
/* */

#audio-player-root {
    width: 100%;
}

.pausedBtn {
    transition: 0.6s;
}

.playedBtn {
    transition: 0.6s;
}

/**
* Pulsating animation
*/

.heart {
    -webkit-transform: scale(1);
    -ms-transform: scale(1);
    -o-transform: scale(1);
    -moz-transform: scale(1);
    transform: scale(1);
    -webkit-transform-origin: center center;
    -moz-transform-origin: center center;
    -ms-transform-origin: center center;
    -o-transform-origin: center center;
    transition: all 6s;
  }
  .heart.css {
    -webkit-animation-delay:6s;
    -moz-animation-delay:6s;
    -ms-animation-delay:6s;
    -o-animation-delay:6s;
    animation-dely:6s;
  }
  .heart.animated {
    -webkit-animation: 6000ms pulsate infinite alternate ease-in-out;
    -moz-animation: 6000ms pulsate infinite alternate ease-in-out;
    -ms-animation: 6000ms pulsate infinite alternate ease-in-out;
    -o-animation: 6000ms pulsate infinite alternate ease-in-out;
    animation: 6000ms pulsate infinite alternate ease-in-out;
  }
  .heart:before,
  .heart:after {
    position: absolute;
    content: "";
    left: 50px;
    top: 0;
    width: 50px;
    height: 80px;
    background: red;
    -moz-border-radius: 50px 50px 0 0;
    border-radius: 50px 50px 0 0;
    -webkit-transform: rotate(-45deg);
       -moz-transform: rotate(-45deg);
        -ms-transform: rotate(-45deg);
         -o-transform: rotate(-45deg);
            transform: rotate(-45deg);
    -webkit-transform-origin: 0 100%;
       -moz-transform-origin: 0 100%;
        -ms-transform-origin: 0 100%;
         -o-transform-origin: 0 100%;
            transform-origin: 0 100%;
  }
  .heart:after {
    left: 0;
    -webkit-transform: rotate(45deg);
       -moz-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
         -o-transform: rotate(45deg);
            transform: rotate(45deg);
    -webkit-transform-origin: 100% 100%;
       -moz-transform-origin: 100% 100%;
        -ms-transform-origin: 100% 100%;
         -o-transform-origin: 100% 100%;
            transform-origin :100% 100%;
  }
  .heart.css:hover {
    -webkit-transform: scale(2);
    -moz-transform: scale(2);
    -ms-transform: scale(2);
    -o-transform: scale(2);
    transform: scale(2);
    -webkit-animation:'';
    -moz-animation:none;
    -ms-animation:'';
    -o-animation:'';
    animation:'';
  }

  @keyframes pulsate {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
  }
  @-webkit-keyframes pulsate {
    0% { -webkit-transform: scale(1); }
    50% { -webkit-transform: scale(1.05); }
    100% { -webkit-transform: scale(1); }
  }
  @-moz-keyframes pulsate {
    0% { -moz-transform: scale(1); }
    50% { -moz-transform: scale(1.05); }
    100% { -moz-transform: scale(1); }
  }
  @-ms-keyframes pulsate {
    0% { -ms-transform: scale(1); }
    50% { -ms-transform: scale(1.05); }
    100% { -ms-transform: scale(1); }
  }
  @-o-keyframes pulsate {
    0% { -o-transform: scale(1); }
    50% { -o-transform: scale(1.05); }
    100% { -o-transform: scale(1); }
  }
</style>
