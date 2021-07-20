@extends('layouts.layout-app-admin')
@section('content')
    <newsfeed inline-template :user_session_details=" {{ Auth::user() }} ">
        <div>
            <share-dialog
                v-if="postToShareData != null"
                :post_details="postToShareData"
                :view_mode="'share_mode'"
                :app_user="user_session_details"
                @close-details-modal="_sharePostDialog_Close"
                @reflect-shared-post="_refreshPostList">
            </share-dialog>

            <post-dialog-v3 :post_preview_watcher="post_preview_watcher"
                :prop_post_details="toPass_postObject"
                :view_mode="'preview_mode'"
                :app_user="user_session_details"
            >
            </post-dialog-v3>


            <!-- CONTENT GRID -->
            <div class="content-grid">
                <!-- GRID -->
                <div class="grid grid-3-6-3 mobile-prefer-content">
                    <!-- GRID COLUMN -->
                    <div class="grid-column">
                        <div class="widget-box box-shadow" style="padding: 0 !important">
                            <div class="user-preview small " :class="app_panel_theme">
                                <figure class="user-preview-cover liquid" style="height: 119px !important">
                                    <img :src="user_session_details.header_cover != null ?
                                        s3_site + 'public/user/' + user_session_details.id + '/cover/' + user_session_details.header_cover :
                                        s3_site + 'public/defaults/default-cover.jpg'" alt="cover-29">
                                </figure>
                                <div class="user-preview-info">
                                    <div class="user-short-description small">
                                        <a class="user-short-description-avatar user-avatar no-stats"
                                            :href="'/user/' + user_session_details.slug"
                                            style="width: 103px !important; height: 108px !important;"
                                        >
                                            <div class="user-avatar-border">
                                                <div class="custom-avatar-tray" :class="app_panel_theme"
                                                    style="width: 108px; height: 108px; border-radius: 50%;">
                                                </div>
                                            </div>

                                            <div
                                                class="user-avatar-content"
                                                :style="[user_session_details.avatar != null ? {'background':'transparent'} : {'background':'linear-gradient(150deg, rgb(76 37 204 / 69%), rgb(255 40 149 / 60%))'}]"
                                                style="
                                                    border-radius: 50%;
                                                    padding: 2px;
                                                    left: 3px !important;
                                                    width: 100% !important;
                                                    text-align: center"
                                            >
                                                <div v-if="user_session_details.avatar != null"
                                                    v-bind:style="{ 'backgroundImage': 'url(' + _getBackgroundImage(user_session_details.avatar) + ')' }"
                                                    style="border-radius: 50% !important;
                                                    background-repeat: no-repeat !important;
                                                    background-position: center !important;
                                                    width: 100% ; height: 96px !important;
                                                    background-size:cover">
                                                </div>
                                                <img v-else src="{{ asset('assets/harmelo/image/default_avatar.png') }}" height="92">
                                            </div>
                                        </a>

                                        <p class="user-short-description-title"><a :class="app_text_theme"
                                                :href="'/user/' + user_session_details.slug">@{{ user_session_details . name }}</a>
                                        </p>
                                        <p class="user-short-description-text" style="text-transform: none !important">
                                            @@{{ user_session_details . slug }} </p>

                                    </div>

                                    <a class="button secondary full text-white" :href="'/user/' + user_session_details.slug">
                                        View Profile
                                    </a>

                                </div>
                            </div>
                            <!-- /USER PREVIEW -->
                        </div>
                        <!-- /WIDGET BOX -->

                        <!-- BANNER PROMO -->
                        <!-- banner-promo removed here -->
                        <!-- /BANNER PROMO -->
                    </div>
                    <!-- /GRID COLUMN -->

                    <!-- GRID COLUMN -->
                    <div class="grid-column">
                        <div class="widget-box no-padding box-shadow" :class="app_panel_theme">
                            <div class="container">
                                <div class="row justify-content-center">
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 text-center" style="padding: 30px !important">
                                        <p class="text-center"><img src="{{ asset('assets/harmelo/svg/post-content.svg') }}" style="width: 100px"></p>
                                        <h1 style="font-size: .7rem; color: #5d6579; margin-bottom: 15px; margin-top: 10px" class="text-center" >
                                            Share your music to the world!
                                        </h1>
                                        <div class="user-preview-actions">
                                            <a href="/upload">
                                                <p class="button secondary full" style="width: 41% !important; height: 35px !important; font-size: .65rem !important; line-height: 33px !important">
                                                    Upload now!
                                                </p>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- WIDGET BOX -->
                        <div class="widget-box no-padding box-shadow" :class="app_panel_theme" v-for="(post, pp) in trackList">
                            <!-- WIDGET BOX SETTINGS -->
                            <div class="widget-box-settings">
                                <!-- POST SETTINGS WRAP -->
                                <div class="post-settings-wrap">

                                    <div class="dropdown">
                                        <a class="dropdown-toggle" :class="app_text_theme" id="dropdownMenuButton" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false"
                                            href="javascript:void(0)">
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="#">Save post</a>
                                            <template v-if="post.user_details[0].id == user_session_details.id">
                                                <a class="dropdown-item" href="#">Edit post</a>
                                                <a class="dropdown-item" href="#">Remove</a>
                                            </template>
                                            <template v-else>
                                                <a class="dropdown-item" href="javascript:void(0)" @click="_hidePost(post.id)">Hide post</a>
                                                <a class="dropdown-item" href="#" >Report post</a>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <!-- /POST SETTINGS WRAP -->
                            </div>
                            <!-- /WIDGET BOX SETTINGS -->

                            <!-- WIDGET BOX STATUS -->
                            <div class="widget-box-status">
                                <!-- WIDGET BOX STATUS CONTENT -->
                                <div class="widget-box-status-content">
                                    <!-- USER STATUS -->
                                    <div class="user-status">
                                        <!-- USER STATUS AVATAR -->
                                        <a class="user-status-avatar" href="profile-timeline.html">
                                            <!-- USER AVATAR -->
                                            <div class="user-avatar small no-outline">
                                                <!-- USER AVATAR CONTENT -->
                                                <div
                                                    class="user-avatar-content"
                                                    :style="[post.user_details[0].avatar != null ? {'background':'transparent'} : {'background':'linear-gradient(150deg, rgb(76 37 204 / 69%), rgb(255 40 149 / 60%))'}]"
                                                    style="
                                                        border-radius: 50%;
                                                        padding: 2px;
                                                        left: 3px !important;
                                                        width: 100% !important;
                                                        text-align: center"
                                                    >
                                                    <img v-if="post.user_details[0].avatar != null"
                                                        :src="!post.user_details[0].avatar.includes('https') ?
                                                            s3_site+'public/user/'+post.user_details[0].id+'/avatar/'+post.user_details[0].avatar
                                                            : post.user_details[0].avatar" height="34" style="border-radius: 50% !important" width="34">
                                                    <img v-else src="{{ asset('assets/harmelo/image/default_avatar.png') }}" height="34" style="border-radius: 50% !important" width="34">
                                                </div>
                                                <!--<div class="user-avatar-content">
                                                    <img v-if="post.user_details[0].avatar != null"
                                                        :src="!post.user_details[0].avatar.includes('https') ?
                                                            s3_site+'public/user/'+post.user_details[0].id+'/avatar/'+post.user_details[0].avatar
                                                            : post.user_details[0].avatar" height="34" style="border-radius: 50% !important" width="34">
                                                    <img v-else src="{{ asset('assets/harmelo/image/default_avatar.png') }}" height="34" style="border-radius: 50% !important" width="34">
                                                </div>-->
                                                <!-- /USER AVATAR CONTENT -->
                                            </div>
                                            <!-- /USER AVATAR -->
                                        </a>
                                        <!-- /USER STATUS AVATAR -->

                                        <!-- USER STATUS TITLE -->
                                        <p class="user-status-title medium">
                                            <a class="bold" :class="app_text_theme" :href="'/user/' + post.user_details[0].slug">
                                                <template v-if="post.user_id == user_session_details.id" :class="app_text_theme">You</template>
                                                <template v-else>@{{ post . user_details[0] . name }}</template>
                                            </a>
                                            <template v-if="post.is_shared == 1">
                                                <span style="font-size: .9rem !important; font-weight: 300 !important" :class="app_text_theme">shared a</span>
                                                <span class="bold" style="font-size: .9rem" :class="app_text_theme">music</span>
                                            </template>
                                            <template v-else>
                                                <span style="font-size: .9rem !important; font-weight: 300 !important" :class="app_text_theme">posted a</span>
                                                <span class="bold" style="font-size: .9rem" :class="app_text_theme">music</span>
                                            </template>
                                        </p>
                                        <!-- /USER STATUS TITLE -->

                                        <!-- USER STATUS TEXT -->
                                        <p class="user-status-text small" :class="app_text_theme"> @{{ moment(post . created_at) . fromNow() }}
                                        </p>
                                        <!-- /USER STATUS TEXT -->
                                    </div>
                                    <!-- /USER STATUS -->

                                    <!-- WIDGET BOX STATUS TEXT -->
                                    <p class="widget-box-status-text" style="margin-top: 30px" :class="app_text_theme">@{{ post . caption }}</p>
                                    <!-- /WIDGET BOX STATUS TEXT -->

                                    <!-- WIDGET BOX -->
                                    <div class="widget-box no-padding box-shadow" :class="app_subpanel_theme" v-if="post.is_shared == 1" style="padding-bottom: 0 !important">
                                        <!-- WIDGET BOX STATUS -->
                                        <div class="widget-box-status">
                                        <!-- WIDGET BOX STATUS CONTENT -->
                                        <div class="widget-box-status-content">
                                            <!-- USER STATUS -->
                                            <div class="user-status">
                                            <!-- USER STATUS AVATAR -->
                                            <a class="user-status-avatar" href="profile-timeline.html">
                                                <!-- USER AVATAR -->
                                                <div class="user-avatar small no-outline">
                                                    <!-- USER AVATAR CONTENT -->
                                                    <div
                                                        class="user-avatar-content"
                                                        :style="[post.original_poster_details[0].avatar != null ? {'background':'transparent'} : {'background':'linear-gradient(150deg, rgb(76 37 204 / 69%), rgb(255 40 149 / 60%))'}]"
                                                        style="
                                                        border-radius: 50%;
                                                        padding: 2px;
                                                        left: 3px !important;
                                                        width: 100% !important;
                                                        text-align: center"
                                                    >
                                                        <img v-if="post.original_poster_details[0].avatar != null"
                                                            :src="!post.original_poster_details[0].avatar.includes('https') ?
                                                                s3_site+'public/user/'+post.original_poster_details[0].id+'/avatar/'+post.original_poster_details[0].avatar
                                                                : post.original_poster_details[0].avatar" height="34" style="border-radius: 50% !important">
                                                        <img v-else src="{{ asset('assets/harmelo/image/default_avatar.png') }}" height="34" style="border-radius: 50% !important">
                                                    </div>
                                                    <!--<div class="user-avatar-content">
                                                        <img v-if="post.original_poster_details[0].avatar != null"
                                                            :src="!post.original_poster_details[0].avatar.includes('https') ?
                                                                s3_site+'public/user/'+post.original_poster_details[0].id+'/avatar/'+post.original_poster_details[0].avatar
                                                                : post.original_poster_details[0].avatar" height="34" style="border-radius: 50% !important">
                                                        <img v-else src="asset('assets/harmelo/image/default_avatar.png')" height="34" style="border-radius: 50% !important">
                                                    </div>-->
                                                    <!-- /USER AVATAR CONTENT -->
                                                </div>
                                                <!-- /USER AVATAR -->
                                            </a>
                                            <!-- /USER STATUS AVATAR -->

                                            <!-- USER STATUS TITLE -->
                                            <p class="user-status-title medium">
                                                <a class="bold" :class="app_text_theme" :href="'/user/' + post.original_poster_details[0].slug">
                                                    <template v-if="post.original_poster_details[0].id == user_session_details.id">You</template>
                                                    <template v-else>@{{ post.original_poster_details[0].name }}</template>
                                                </a>
                                            </p>
                                            <!-- /USER STATUS TITLE -->

                                            <!-- USER STATUS TEXT -->
                                            <p class="user-status-text small">@{{ moment(post.original_post_details[0].created_at).fromNow() }}</p>
                                            <!-- /USER STATUS TEXT -->
                                            </div>
                                            <!-- /USER STATUS -->

                                            <!-- WIDGET BOX STATUS TEXT -->
                                            <p class="widget-box-status-text">@{{ post.original_post_details[0].caption }}</p>
                                            <!-- /WIDGET BOX STATUS TEXT -->
                                        </div>
                                        <!-- /WIDGET BOX STATUS CONTENT -->

                                        <!-- IFRAME WRAP -->
                                        <div class="iframe-wrap" style="padding-top: 0.25% !important; padding: 10px !important">

                                            <audio style="display:none" ref="playerId" :id="'hover_playerId' + post.id"
                                                muted="true">
                                                <source :src="s3_site + post.track_details[0].track_path" type="audio/*" />
                                                <!--  -->
                                            </audio>
                                            <button id="fake-btn" style="display: none"></button>

                                            <!-- VIDEO BOX -->
                                            <div class="video-box small popup-picture-trigger" @click="_loadPostPreview(post)">
                                                <!--   popup-picture-trigger-->
                                                <!-- VIDEO BOX COVER -->
                                                <div class="video-box-cover">
                                                    <!-- VIDEO BOX COVER IMAGE -->

                                                    <figure class="video-box-cover-image liquid" style="height: 400px !important">
                                                        <img :src="post.track_details[0].image_path != null ? s3_site + post.track_details[0].image_path : defaultThumbnail" alt="cover-08"
                                                            style="border-radius: 12px">
                                                    </figure>
                                                    <!-- /VIDEO BOX COVER IMAGE -->

                                                    <!-- PLAY BUTTON -->
                                                    <div class="play-button post__playBtn">
                                                        <!-- PLAY BUTTON ICON -->
                                                        <img src="{{ asset('assets/harmelo/logo/harmelo-logo-no-word-white.png') }}"
                                                            height="150">
                                                    </div>
                                                    <!-- /PLAY BUTTON -->

                                                    <!-- VIDEO BOX INFO -->
                                                    <div class="video-box-info">
                                                        <!-- VIDEO BOX TITLE -->
                                                        <p class="video-box-title">@{{ post.original_poster_details[0].name }} - @{{ post.track_details[0].title }} </p>
                                                        <!-- /VIDEO BOX TITLE -->

                                                        <!-- VIDEO BOX TEXT -->
                                                        <p class="video-box-text" v-if="post.track_details[0].producer != null">@{{ post.track_details[0].producer }}</p>
                                                        <!-- /VIDEO BOX TEXT -->
                                                    </div>
                                                    <!-- /VIDEO BOX INFO -->
                                                </div>
                                                <!-- /VIDEO BOX COVER -->
                                            </div>
                                            <!-- /VIDEO BOX -->
                                        </div>
                                        <!-- /IFRAME WRAP -->
                                        <!-- VIDEO STATUS -->
                                        <a class="video-status" href="https://www.twitch.tv/" target="_blank" style="margin-top: 0 !important">
                                            <!-- VIDEO STATUS INFO -->
                                            <div class="video-status-info" :class="app_subpanel_theme">

                                                <!-- VIDEO STATUS TITLE -->
                                                <p class="video-status-title"><span class="highlighted">@{{ post.track_details[0].title }}</span></p>
                                                <!-- /VIDEO STATUS TITLE -->

                                                <!-- VIDEO STATUS TEXT -->
                                                <p class="video-status-text">Produced by @{{ post.track_details[0].producer }}</p>
                                                <!-- /VIDEO STATUS TEXT -->
                                            </div>
                                            <!-- /VIDEO STATUS INFO -->
                                        </a>
                                        <!-- /VIDEO STATUS -->
                                        </div>
                                        <!-- /WIDGET BOX STATUS -->
                                    </div>
                                    <!-- /WIDGET BOX -->

                                </div>
                                <!-- /WIDGET BOX STATUS CONTENT -->

                                <!-- IFRAME WRAP -->
                                <div class="iframe-wrap" style="padding-top: 0.25% !important;" v-if="post.is_shared == 0">
                                    <!-- <iframe src="https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3" allowfullscreen>
                                                                    </iframe> -->

                                    <audio style="display:none" ref="playerId" :id="'hover_playerId' + post.id"
                                        muted="true">
                                        <source :src="s3_site + post.track_details[0].track_path" type="audio/*" />
                                        <!--  -->
                                    </audio>
                                    <button id="fake-btn" style="display: none"></button>

                                    <!-- VIDEO BOX -->
                                    <div class="video-box small popup-picture-trigger"  @click="_loadPostPreview(post)" > <!-- data-toggle="modal" data-target="#post-preview" -->
                                        <!--   popup-picture-trigger-->
                                        <!-- VIDEO BOX COVER -->
                                        <div class="video-box-cover">
                                            <!-- VIDEO BOX COVER IMAGE -->

                                            <figure class="video-box-cover-image liquid" style="height: 400px !important">
                                                <img :src="post.track_details[0].image_path != null ? s3_site + post.track_details[0].image_path : defaultThumbnail" alt="cover-08"
                                                    style="border-radius: 12px">
                                            </figure>
                                            <!-- /VIDEO BOX COVER IMAGE -->

                                            <!-- PLAY BUTTON -->
                                            <div class="play-button post__playBtn">
                                                <!-- PLAY BUTTON ICON -->
                                                <img src="{{ asset('assets/harmelo/logo/harmelo-logo-no-word-white.png') }}"
                                                    height="150">
                                                <!-- <svg class="play-button-icon icon-play" style="    width: 28px !important; height: 29px !important;">
                                                                    <use xlink:href="#svg-play"></use>
                                                                    </svg> -->
                                                <!-- /PLAY BUTTON ICON -->
                                            </div>
                                            <!-- /PLAY BUTTON -->

                                            <!-- VIDEO BOX INFO -->
                                            <div class="video-box-info">
                                                <!-- VIDEO BOX TITLE -->
                                                <p class="video-box-title">@{{ post.user_details[0].name }} - @{{ post.track_details[0].title }} </p>
                                                <!-- /VIDEO BOX TITLE -->

                                                <!-- VIDEO BOX TEXT -->
                                                <p class="video-box-text" v-if="post.track_details[0].producer != null">@{{ post.track_details[0].producer }}</p>
                                                <!-- /VIDEO BOX TEXT -->
                                            </div>
                                            <!-- /VIDEO BOX INFO -->
                                        </div>
                                        <!-- /VIDEO BOX COVER -->
                                    </div>
                                    <!-- /VIDEO BOX -->
                                </div>
                                <!-- /IFRAME WRAP -->

                                <!-- WIDGET BOX STATUS CONTENT -->
                                <div class="widget-box-status-content">
                                    <!-- <div class="tag-list">
                                        <a class="tag-item secondary" href="newsfeed.html">Stream</a>

                                        <a class="tag-item secondary" href="newsfeed.html">StrikerGO</a>

                                        <a class="tag-item secondary" href="newsfeed.html">Outlaws</a>

                                        <a class="tag-item secondary" href="newsfeed.html">Gaming</a>
                                    </div> -->

                                    <!-- CONTENT ACTIONS -->
                                    <div class="content-actions" :class="app_bordertop_theme">
                                        <!-- CONTENT ACTION -->
                                        <div class="content-action">
                                            <!-- META LINE -->
                                            <div class="meta-line" v-if="post.reactions.length > 0">
                                                <!-- META LINE LIST -->
                                                <div class="meta-line-list reaction-item-list">
                                                    <!-- REACTION ITEM -->
                                                    <div class="reaction-item" v-if="post.reactions.filter(e => e.reaction_type === 'like').length > 0">
                                                        <img class="reaction-image"
                                                            src="{{ asset('assets/vikinger/img/reaction/like.png') }}"
                                                            alt="reaction-like">
                                                    </div>
                                                    <!-- /REACTION ITEM -->

                                                    <!-- REACTION ITEM -->
                                                    <div class="reaction-item" v-if="post.reactions.filter(e => e.reaction_type === 'love').length > 0">
                                                        <img class="reaction-image"
                                                            src="{{ asset('assets/vikinger/img/reaction/love.png') }}"
                                                            alt="reaction-love">
                                                    </div>
                                                    <!-- /REACTION ITEM -->

                                                    <!-- REACTION ITEM -->
                                                    <div class="reaction-item" v-if="post.reactions.filter(e => e.reaction_type === 'funny').length > 0">
                                                        <img class="reaction-image"
                                                            src="{{ asset('assets/vikinger/img/reaction/funny.png') }}"
                                                            alt="reaction-happy">
                                                    </div>
                                                    <!-- /REACTION ITEM -->

                                                    <!-- REACTION ITEM -->
                                                    <div class="reaction-item" v-if="post.reactions.filter(e => e.reaction_type === 'wow').length > 0">
                                                        <img class="reaction-image"
                                                            src="{{ asset('assets/vikinger/img/reaction/wow.png') }}"
                                                            alt="reaction-wow">
                                                    </div>
                                                    <!-- /REACTION ITEM -->

                                                    <!-- REACTION ITEM -->
                                                    <div class="reaction-item" v-if="post.reactions.filter(e => e.reaction_type === 'sad').length > 0">
                                                        <img class="reaction-image"
                                                            src="{{ asset('assets/vikinger/img/reaction/sad.png') }}"
                                                            alt="reaction-sad">
                                                    </div>
                                                    <!-- /REACTION ITEM -->
                                                </div>
                                                <!-- /META LINE LIST -->

                                                <!-- META LINE TEXT -->
                                                <p class="meta-line-text" :class="app_text_theme">@{{ post.reactions.length }}</p>
                                                <!-- /META LINE TEXT -->
                                            </div>
                                            <!-- /META LINE -->

                                            <!-- META LINE -->
                                            <div class="meta-line">
                                            </div>
                                            <!-- /META LINE -->
                                        </div>
                                        <!-- /CONTENT ACTION -->

                                        <!-- CONTENT ACTION -->
                                        <div class="content-action">
                                            <!-- META LINE -->
                                            <div class="meta-line">
                                                <!-- META LINE LINK -->
                                                <p class="meta-line-link" :class="app_text_theme">2 Comments</p>
                                                <!-- /META LINE LINK -->
                                            </div>
                                            <!-- /META LINE -->

                                            <!-- META LINE -->
                                            <div class="meta-line">
                                                <!-- META LINE TEXT -->
                                                <p class="meta-line-text" :class="app_text_theme" v-if="post.shares.length > 0">
                                                    @{{ post.shares.length }} Share<template v-if="post.shares.length > 1">s</template>
                                                </p>
                                                <!-- /META LINE TEXT -->
                                            </div>
                                            <!-- /META LINE -->
                                        </div>
                                        <!-- /CONTENT ACTION -->
                                    </div>
                                    <!-- /CONTENT ACTIONS -->
                                </div>
                                <!-- /WIDGET BOX STATUS CONTENT -->
                            </div>
                            <!-- /WIDGET BOX STATUS -->

                            <!-- POST OPTIONS -->
                            <div class="post-options" :class="[app_panel_theme, app_bordertop_theme]">
                                <!-- POST OPTION WRAP -->
                                <div class="post-actions" style="width: 160px !important">
                                    <div class="dropup">
                                        <p class="ml-2 post-option-text" :class="app_text_theme" type="button" data-toggle="dropdown" style="font-size: .80rem">
                                            <img class="reaction-option-image" height="20px" style="margin-right: 10px !important"
                                                v-if="post.reactions.filter(e => e.reacted_by == user_session_details.id).length > 0"
                                                :src="_getImageSource(post.reactions, user_session_details.id)"
                                                alt="reaction-like"
                                            >
                                            <svg class="post-option-icon icon-thumbs-up"
                                                v-else>
                                                <use xlink:href="#svg-thumbs-up"></use>
                                            </svg>
                                            React!
                                        </p>
                                        <ul class="dropdown-menu" :class="app_panel_theme" style="border-radius: 22px !important; width: 400px !important; text-align: center !important">
                                            <li class="reaction_list" style="margin: 5px 13px !important; cursor: pointer" v-for="(react, rr_react) in ['like', 'love', 'funny', 'wow', 'sad']"
                                                @click="post.reactions.filter(e => e.reacted_by == user_session_details.id).length > 0
                                                    ?
                                                musicfeedupdatereacttopost(post.reactions, user_session_details.id, react, post.id)
                                                    :
                                                musicfeedreacttopost(post.id, react)">
                                                <img class="reaction-option-image"
                                                :src="'{{ asset('assets/vikinger/img/reaction/')   }}' + '/' + react + '.png' "
                                                :alt="'reaction-'+react">
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <!-- /POST OPTION WRAP -->

                                <!-- POST OPTION -->
                                <div class="post-actions" style="width: 160px !important">
                                    <!-- POST OPTION ICON -->
                                    <svg class="post-option-icon icon-comment">
                                        <use xlink:href="#svg-comment"></use>
                                    </svg>
                                    <!-- /POST OPTION ICON -->

                                    <!-- POST OPTION TEXT -->
                                    <p class="ml-2 post-option-text" :class="app_text_theme" style="font-size: 0.8rem !important">Comment</p>
                                    <!-- /POST OPTION TEXT -->
                                </div>
                                <!-- /POST OPTION -->

                                <!-- POST OPTION -->
                                <div class="post-actions" style="width: 160px !important"
                                    v-if="post.shares.filter(e => e.shared_by == user_session_details.id).length > 0">
                                    <svg class="post-option-icon icon-share">
                                        <use xlink:href="#svg-share"></use>
                                    </svg>
                                    <p class="ml-2 post-option-text" :class="app_text_theme" style="font-size: 0.8rem !important">Shared</p>
                                </div>
                                <div class="post-actions" style="width: 160px !important" @click="_sharePostDialog_Open(post)"
                                    v-else> <!--  popup-manage-group-trigger -->
                                    <svg class="post-option-icon icon-share">
                                        <use xlink:href="#svg-share"></use>
                                    </svg>
                                    <p class="ml-2 post-option-text" :class="app_text_theme" style="font-size: 0.8rem !important">Share</p>
                                </div>
                                <!-- /POST OPTION -->
                            </div>
                            <!-- /POST OPTIONS -->
                        </div>
                        <!-- /WIDGET BOX -->


                        <!-- NEW USER WELCOME BLOCK -->
                        <!-- WIDGET BOX -->
                        <div class="widget-box no-padding box-shadow" :class="app_panel_theme" v-if="!isADayAgo(moment(user_session_details.created_at))">
                            <!-- WIDGET BOX STATUS -->
                            <div class="widget-box-status">
                            <!-- WIDGET BOX STATUS CONTENT -->
                            <div class="widget-box-status-content">
                                <!-- USER STATUS -->
                                <div class="user-status">
                                    <a class="user-status-avatar" href="profile-timeline.html">
                                        <div class="user-avatar small no-outline">
                                            <div
                                                class="user-avatar-content"
                                                style="
                                                    background: linear-gradient(150deg, rgb(76 37 204 / 69%), rgb(255 40 149 / 60%));
                                                    border-radius: 50%;
                                                    padding: 2px;
                                                    left: 3px !important;
                                                    width: 100% !important;
                                                    text-align: center">
                                                <img src="{{ asset('assets/harmelo/image/default_avatar.png') }}" height="34" style="border-radius: 50% !important">
                                            </div>
                                            <!--<div class="user-avatar-content">
                                                <img src="asset('assets/harmelo/image/default_avatar.png')" height="34" style="border-radius: 50% !important">
                                            </div>-->
                                        </div>
                                    </a>

                                <!-- USER STATUS TITLE -->
                                <div class="user-status-title medium" style="padding-top: 8px"><a class="bold" :class="app_text_theme" href="profile-timeline.html">Harmelo Team</a></div>
                                <!-- /USER STATUS TITLE -->
                                </div>
                                <!-- /USER STATUS -->

                                <!-- QUOTE BOX -->
                                <blockquote class="quote-box" :class="app_panel_theme">
                                    <svg class="quote-box-icon icon-quote" :style="[user_session_details.app_theme === 'light' ? {'fill': '#000'} : {'fill':'#fff'}]">
                                        <use xlink:href="#svg-quote"></use>
                                    </svg>

                                    <p class="quote-box-text" :class="app_text_theme">Hooray! Welcome to
                                        <span style="color: #31e3ed !important">&#127926</span> Harmelo <span style="color: #dd2bea !important">&#129345</span>
                                        Find friends and begin your music journey by sharing your composition to the world!
                                    </p>
                                </blockquote>
                                <!-- /QUOTE BOX -->
                            </div>
                            <!-- /WIDGET BOX STATUS CONTENT -->
                            </div>
                            <!-- /WIDGET BOX STATUS -->
                        </div>
                        <!-- /WIDGET BOX -->
                        <!-- END OF NEW USER WELCOME BLOCK -->

                        <p style="text-align: center; color: #fff; margin-top: 30px" @click="musicfeedgetolderpost()" v-if="track_postList.length > 10"><a href="javascript:void(0)" :class="app_text_theme">Show older posts</a></p>
                        <!-- LOADER BARS -->
                        <div class="loader-bars" v-show="is_fetching_older_post">
                            <div class="loader-bar"></div>
                            <div class="loader-bar"></div>
                            <div class="loader-bar"></div>
                            <div class="loader-bar"></div>
                            <div class="loader-bar"></div>
                            <div class="loader-bar"></div>
                            <div class="loader-bar"></div>
                            <div class="loader-bar"></div>
                        </div>
                        <!-- /LOADER BARS -->
                    </div>
                    <!-- /GRID COLUMN -->


                    <!-- GRID COLUMN -->
                    <div class="grid-column">

                        <!-- blog articles here -->
                        <div class="stats-box-slider">
                            <div class="stats-box-slider-controls">
                                <p class="stats-box-slider-controls-title">Latest Articles</p>
                                <div id="stats-box-slider-controls" class="slider-controls">
                                    <div class="slider-control negative left">
                                        <svg class="slider-control-icon icon-small-arrow">
                                            <use xlink:href="#svg-small-arrow"></use>
                                        </svg>
                                    </div>
                                    <div class="slider-control negative right">
                                        <svg class="slider-control-icon icon-small-arrow">
                                            <use xlink:href="#svg-small-arrow"></use>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <!-- /STATS BOX SLIDER CONTROLS -->

                            <!-- BLOGS SLIDER ITEMS -->
                            <div id="stats-box-slider-items" class="stats-box-slider-items" v-if="blog_posts.length > 0">
                                <div class="stats-box stat-profile-views"
                                    :style="{ 'backgroundImage': 'linear-gradient(0deg, #7750f8b0, #7750f86b), url(' + blog_posts[0]._embedded['wp:featuredmedia'][0].source_url + ')' }">

                                    <p class="stats-box-title" style="margin-top: 67px !important">
                                        @{{ blog_posts[0] . title . rendered . substring(0, 23) }}...</p>

                                    <p class="stats-box-text">@{{ moment(blog_posts[0] . date) . fromNow() }}</p>
                                </div>
                            </div>
                            <!-- /BLOGS SLIDER ITEMS -->
                        </div>
                        <!-- blog articles ends here -->

                        <!-- WIDGET BOX -->
                        <div class="widget-box box-shadow" :class="app_panel_theme">
                            <div class="widget-box-settings">
                                <div class="post-settings-wrap">

                                    <div class="post-settings widget-box-post-settings-dropdown-trigger">
                                        <svg class="post-settings-icon icon-more-dots">
                                            <use xlink:href="#svg-more-dots"></use>
                                        </svg>
                                    </div>

                                    <div class="simple-dropdown widget-box-post-settings-dropdown">
                                        <p class="simple-dropdown-link">Widget Settings</p>
                                    </div>
                                </div>
                            </div>

                            <p class="widget-box-title" :class="app_text_theme">People you may know</p>

                            <div class="widget-box-content" v-if="random_suggested_users != null">

                                <!-- USER STATUS LIST -->
                                <div class="user-status-list">
                                    <div class="user-status request-small" v-for="(random_suggested, rr_ss) in random_suggested_users" :key="rr_ss">

                                        <a class="user-status-avatar" :href="'user/'+random_suggested.slug">
                                            <!-- USER AVATAR -->
                                            <div class="user-avatar small no-outline">

                                                <div class="user-avatar-content">
                                                    <img v-if="random_suggested.avatar != null"
                                                        :src="!random_suggested.avatar.includes('https') ?
                                                        s3_site+'public/user/'+random_suggested.id+'/avatar/'+random_suggested.avatar
                                                        : random_suggested.avatar" height="32"
                                                        style="border-radius: 50%; height: 32px; width: 32px">
                                                    <img v-else src="{{ asset('assets/harmelo/image/default_avatar.png') }}" height="32"
                                                        style="border-radius: 50%">
                                                </div>

                                            </div>
                                            <!-- /USER AVATAR -->
                                        </a>

                                        <p class="user-status-title"><a class="bold" :class="app_text_theme" :href="'user/'+random_suggested.slug">@{{ random_suggested.name }}</a></p>

                                        <p class="user-status-text small">24.5K profile views</p>

                                        <!-- ACTION REQUEST LIST -->
                                        <div class="action-request-list">
                                            <div class="action-request accept" @click="userfollowerfollowuser(random_suggested.id)">
                                                <svg class="action-request-icon icon-add-friend">
                                                    <use xlink:href="#svg-add-friend"></use>
                                                </svg>
                                            </div>
                                        </div>
                                        <!-- ACTION REQUEST LIST -->
                                    </div>
                                    <!-- /USER STATUS -->
                                </div>
                                <!-- /USER STATUS LIST -->
                            </div>
                            <!-- WIDGET BOX CONTENT -->
                        </div>
                        <!-- /WIDGET BOX -->


                        <!-- WIDGET BOX -->
                        <div class="widget-box box-shadow" :class="app_panel_theme">
                            <div class="widget-box-controls">
                                <div id="reaction-stat-slider-controls" class="slider-controls">
                                    <div class="slider-control left">
                                        <svg class="slider-control-icon icon-small-arrow">
                                            <use xlink:href="#svg-small-arrow"></use>
                                        </svg>
                                    </div>

                                    <div class="slider-control right">
                                        <svg class="slider-control-icon icon-small-arrow">
                                            <use xlink:href="#svg-small-arrow"></use>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <p class="widget-box-title" :class="app_text_theme">Reactions Received</p>

                            <div class="widget-box-content">
                                <div id="reaction-stat-slider-items" class="widget-box-content-slider">
                                    <div class="widget-box-content-slider-item">
                                        <div class="reaction-stats">
                                            <div class="reaction-stat">
                                                <img class="reaction-stat-image"
                                                    src="{{ asset('assets/vikinger/img/reaction/like.png') }}"
                                                    alt="reaction-like">

                                                <p class="reaction-stat-title" :class="app_text_theme">
                                                    <template v-if="'like' in app_user_reactions_received">@{{ app_user_reactions_received.like }}</template>
                                                    <template v-else>0</template>
                                                </p>

                                                <p class="reaction-stat-text">Likes</p>
                                            </div>

                                            <div class="reaction-stat">
                                                <img class="reaction-stat-image"
                                                    src="{{ asset('assets/vikinger/img/reaction/love.png') }}"
                                                    alt="reaction-love">

                                                <p class="reaction-stat-title" :class="app_text_theme">
                                                    <template v-if="'love' in app_user_reactions_received">@{{ app_user_reactions_received.love }}</template>
                                                    <template v-else>0</template>
                                                </p>

                                                <p class="reaction-stat-text">Loves</p>
                                            </div>
                                        </div>

                                        <div class="reaction-stats">
                                            <div class="reaction-stat">
                                                <img class="reaction-stat-image"
                                                    src="{{ asset('assets/vikinger/img/reaction/dislike.png') }}"
                                                    alt="reaction-dislike">

                                                <p class="reaction-stat-title" :class="app_text_theme">
                                                    <template v-if="'wow' in app_user_reactions_received">@{{ app_user_reactions_received.wow }}</template>
                                                    <template v-else>0</template>
                                                </p>

                                                <p class="reaction-stat-text">Wow</p>
                                            </div>

                                            <div class="reaction-stat">
                                                <img class="reaction-stat-image"
                                                    src="{{ asset('assets/vikinger/img/reaction/happy.png') }}"
                                                    alt="reaction-happy">

                                                <p class="reaction-stat-title" :class="app_text_theme">
                                                    <template v-if="'funny' in app_user_reactions_received">@{{ app_user_reactions_received.funny }}</template>
                                                    <template v-else>0</template>
                                                </p>

                                                <p class="reaction-stat-text">Happy</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="widget-box-content-slider-item">
                                        <div class="reaction-stats">
                                            <div class="reaction-stat">
                                                <img class="reaction-stat-image"
                                                    src="{{ asset('assets/vikinger/img/reaction/funny.png') }}"
                                                    alt="reaction-funny">

                                                <p class="reaction-stat-title" :class="app_text_theme">
                                                    <template v-if="'sad' in app_user_reactions_received">@{{ app_user_reactions_received.sad }}</template>
                                                    <template v-else>0</template>
                                                </p>

                                                <p class="reaction-stat-text">Sad</p>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /WIDGET BOX CONTENT SLIDER ITEM -->
                                </div>
                                <!-- /WIDGET BOX CONTENT SLIDER -->
                            </div>
                        </div>
                        <!-- /WIDGET BOX -->

                    </div>
                    <!-- /GRID COLUMN -->
                </div>
                <!-- /GRID -->
            </div>
            <!-- /CONTENT GRID -->

            <!-- POPUP PICTURE -->
            <!-- <post-dialogv2 v-if="toPass_postObject != null"
                url="https://file-examples-com.github.io/uploads/2017/11/file_example_MP3_5MG.mp3"
                playerid="audio-player"
                albumart=""
                :post_details="toPass_postObject"
                view_mode="preview_mode">
            </post-dialogv2> -->

        </div>
        <!-- /POPUP PICTURE -->
    </newsfeed>

    <link href="{{ asset('assets/harmelo/css/newsfeed.css') }}" rel="stylesheet">

@endsection

@section('page_stylesheets')

@endsection
