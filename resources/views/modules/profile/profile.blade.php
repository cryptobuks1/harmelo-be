@extends('layouts.layout-app-admin')
@section('content')
    <profile inline-template>
        <div>
            <post-dialog-v3 :post_preview_watcher="post_preview_watcher"
                :prop_post_details="toPass_postObject"
                :view_mode="'preview_mode'"
                :app_user="app_user_details"
            >
            </post-dialog-v3>
            <!-- CONTENT GRID -->
            <div class="content-grid">
                <div class="profile-header v2" :class="app_panel_theme">
                    <figure class="profile-header-cover liquid" v-if="user_details != null" :style="[cover_edit_mode ? {'cursor':'move'} : {}]">
                        <div class="progress" style="height: 5px; margin-right: 5px; margin-left: 5px; border-radius: 0 !important" v-if="cover_uploadProgress != 0 && cover_uploadProgress != 100">
                            <div class="progress-bar-purple progress-bar-striped progress-bar-animated"
                                role="progressbar"
                                :style="{'width': cover_uploadProgress + '%'}"
                                :aria-valuenow="cover_uploadProgress"
                                aria-valuemin="0"
                                aria-valuemax="100">
                            </div>
                        </div>
                        <cropper v-if="cover_edit_mode"
                            ref="covercropper"
                            :src="cover_img_rc"
                            :stencil-props="{
                                handlers: {},
                                movable: false,
                                resizable: false,
                            }"
                            image-restriction="fill-area"
                            :max-height="100"
                            :max-width="100"
                            :min-height="100"
                            :min-width="100"
                            :size-restrictions-algorithm="percentsRestriction"
                        ></cropper>
                        <img v-if="!cover_edit_mode"
                            :src="user_details.header_cover != null ?
                            s3_site + 'public/user/' + user_details.id + '/cover/' + user_details.header_cover :
                            s3_site + 'public/defaults/default-cover.jpg'" alt="cover-01" style="border-top-left-radius: 12px;
                            border-top-right-radius: 12px;"
                        >
                    </figure>
                    <input
                        :disabled="is_uploading_cover"
                        type="file"
                        id="cover_photo_fileinput"
                        accept="image/*"
                        style="display: none"
                        @change="loadImage($event)"
                        @click="resetCoverInputValue($event)"
                    >

                    <div class="profile-header-info">
                      <div class="user-short-description big">
                        <a class="user-short-description-avatar user-avatar big no-stats" href="javascript:void(0)">
                          <div class="user-avatar-border">
                            <div class="custom-avatar-tray" :class="app_panel_theme"
                                style="width: 153.3px; height: 153.3px; border-radius: 50%;">
                            </div>
                          </div>
                            <template v-if="user_details != null">
                                <div
                                    class="user-avatar-content"
                                    :style="[user_details.avatar != null
                                        ? {'background':'transparent', 'left':'3px !important', 'top':'3.3px !important'}
                                        : {'background':'linear-gradient(150deg, rgb(76 37 204 / 69%), rgb(255 40 149 / 60%))', 'left':'5px !important', 'top':'4.3px !important'}]"
                                    style="
                                    border-radius: 50%;
                                    padding: 2px;"
                                >
                                    <img v-if="user_details.avatar != null"
                                        :src="_getBackgroundImage(user_details.avatar)"
                                        style="border-radius: 50% !important;
                                        background-position: center !important;
                                        width: 143px !important;
                                        height: 143px !important"
                                    >
                                    <img v-else src="{{ asset('assets/harmelo/image/default_avatar.png') }}"
                                        style="border-radius: 50% !important;
                                        background-position: center !important;
                                        width: 100% !important;
                                        height: 100% !important"
                                    >
                                </div>
                            </template>
                        </a>
                        <template v-if="user_details != null">
                            <p class="user-short-description-title"><a href="javascript:void(0)" :class="app_text_theme">@{{ user_details . name }}</a></p>
                            <p class="user-short-description-text" v-if="user_details.bio != null" :class="app_text_theme">@{{ user_details . bio }}</p>
                        </template>
                      </div>

                      <div class="user-stats" style="right: 5px !important">
                        <div class="user-stat big" style="cursor: pointer"
                            @click="section_type = 'followings'">
                            <p class="user-stat-title" :class="app_text_theme">@{{ user_profile_followings.length }}</p>
                            <p class="user-stat-text" :class="app_text_theme">Followings</p>
                        </div>

                        <div class="user-stat big" style="cursor: pointer"
                            @click="section_type = 'followers'">
                          <p class="user-stat-title" :class="app_text_theme">@{{ user_profile_followers.length }}</p>
                          <p class="user-stat-text" :class="app_text_theme">Followers</p>
                        </div>

                        <div class="user-stat big" style="cursor: pointer"
                            @click="section_type = 'posts'">
                          <p class="user-stat-title" :class="app_text_theme">@{{ user_profile_post_list.length }}</p>
                          <p class="user-stat-text" :class="app_text_theme">posts</p>
                        </div>

                        <div class="user-stat big" style="cursor: pointer"
                            @click="section_type = 'visitors'">
                          <p class="user-stat-title" :class="app_text_theme">@{{ user_profile_visitors.length }}</p>
                          <p class="user-stat-text" :class="app_text_theme">visits</p>
                        </div>
                      </div>

                        <template v-if="user_details != null">

                            <template v-if="app_user != null">
                                <!-- IF APP USER OPENS HIS/HER PROFILE -->
                                <div class="profile-header-info-actions"
                                    v-if="_isSelf(user_details.id)"
                                    style="top: -38px !important">
                                    <!-- IF COVER ADDED/EDITED -->
                                    <template v-if="cover_edit_mode">
                                        <p
                                            class="profile-header-info-action button secondary"
                                            v-if="cover_uploadProgress != 0 && cover_uploadProgress != 100"
                                            style="background-color: #39ad39 !important; width: 180px !important"
                                        >
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            Loading...
                                        </p>
                                        <p class="profile-header-info-action button secondary" v-else
                                            @click="_cropCover()"
                                            style="background-color: #39ad39 !important; width: 180px !important"
                                        >
                                            <span class="hide-text-mobile">Save Changes</span>
                                        </p>
                                        <p class="profile-header-info-action button secondary"
                                            @click="_discardCoverChange()"
                                            style="background-color: #fb4f4f !important; width: 180px !important"
                                        >
                                            <span class="hide-text-mobile">Discard Changes</span>
                                        </p>
                                    </template>
                                    <!-- IF NO ACTIONS -->
                                    <template v-else>
                                        <div class="dropdown">
                                            <p  class="profile-header-info-action button secondary dropdown-toggle"
                                                data-toggle="dropdown"
                                                style="background-color: #3f249c !important; width: 180px !important"
                                            >
                                                <span class="hide-text-mobile">Image Settings</span>
                                                <span class="caret"></span>
                                            </p>
                                            <ul class="dropdown-menu" style="min-width: 11.1rem !important; padding: 10px !important; border-radius: 12px !important; text-align: left !important;">
                                                <li style="padding: 10px" @click="_triggerFileInput('cover_photo_fileinput')"><a href="javascript:void(0)">Add Cover Photo</a></li>
                                                <li style="padding: 10px" @click="_redirectTo('/user/settings/'+user_details.slug)"><a href="javascript:void(0)">Add Profile Picture</a></li>
                                            </ul>
                                        </div>
                                        <div class="dropdown">
                                            <p  class="profile-header-info-action button secondary dropdown-toggle"
                                                data-toggle="dropdown"
                                                style="background-color: #dd2bea !important; width: 180px !important"
                                            >
                                                <span class="hide-text-mobile">Edit Profile</span>
                                                <span class="caret"></span>
                                            </p>
                                            <ul class="dropdown-menu" style="min-width: 11.1rem !important; padding: 10px !important; border-radius: 12px !important; text-align: left !important;">
                                                <li style="padding: 10px" @click="_redirectTo('/user/settings/'+user_details.slug)"><a href="javascript:void(0)">Edit Profile</a></li>
                                                <li style="padding: 10px">
                                                    <a :href="'http://192.168.1.118:8080/access_code/' + user_details.access_code + '/studio'">Edit Studio</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </template>
                                </div>

                                <!-- IF BOTH USER FOLLOWS EACH OTHER : FRIENDS -->
                                <div class="profile-header-info-actions" v-if="_isFriendWithUser(user_details.id)"
                                    style="top: -38px !important"
                                >
                                    <div class="dropdown">
                                        <p  class="profile-header-info-action button secondary dropdown-toggle"
                                            data-toggle="dropdown"
                                            style="background-color: #39ad39 !important; width: 180px !important"
                                        >
                                            <span class="hide-text-mobile">Friends</span>
                                            <span class="caret"></span>
                                        </p>
                                        <ul class="dropdown-menu" style="min-width: 11.1rem !important; padding: 10px !important; border-radius: 12px !important; text-align: left !important;">
                                        <li style="padding: 10px"><a href="javascript:void(0)">Unfollow</a></li>
                                        </ul>
                                    </div>
                                    <p class="profile-header-info-action button primary"
                                        style="width: 180px !important"
                                    >
                                        <span class="hide-text-mobile">Send</span>
                                        Message
                                    </p>
                                </div>

                                <!-- IF APP USER IS FOLLOWING THIS USER -->
                                <div class="profile-header-info-actions"
                                    v-if="!_isFriendWithUser(user_details.id) && _isFollowingUser(user_details.id)"
                                    style="top: -38px !important"
                                >
                                    <div class="dropdown">
                                        <p  class="profile-header-info-action button secondary dropdown-toggle"
                                            data-toggle="dropdown"
                                            style="background-color: #39ad39 !important; width: 180px !important"
                                        >
                                            <span class="hide-text-mobile">Following</span>
                                            <span class="caret"></span>
                                        </p>
                                        <ul class="dropdown-menu" style="min-width: 11.1rem !important; padding: 10px !important; border-radius: 12px !important; text-align: left !important;">
                                        <li style="padding: 10px"><a href="javascript:void(0)">Unfollow</a></li>
                                        </ul>
                                    </div>
                                    <p class="profile-header-info-action button primary"
                                        style="width: 180px !important"
                                    >
                                        <span class="hide-text-mobile">Send</span>
                                        Message
                                    </p>
                                </div>

                                <!-- IF APP USER IS FOLLOWING THIS USER AND IS PENDING-->
                                <div class="profile-header-info-actions"
                                    v-if="_isPendingFollowingUser(user_details.id)"
                                    style="top: -38px !important"
                                >
                                    <p class="profile-header-info-action button secondary"
                                        style="background-color: #fb4f4f !important; width: 180px !important"
                                        @click="userfollowercancelrequest(user_details.id)">
                                        <span class="hide-text-mobile">Cancel</span>
                                        Request
                                    </p>
                                    <p class="profile-header-info-action button primary"
                                        style="width: 180px !important">
                                        <span class="hide-text-mobile">Send</span>
                                        Message
                                    </p>
                                </div>

                                <!-- IF APP USER IS FOLLOWED BY THIS USER AND IS PENDING-->
                                <div class="profile-header-info-actions"
                                    v-if="_isPendingFollowerUser(user_details.id)"
                                    style="top: -38px !important"
                                >
                                    <div class="dropdown">
                                        <p  class="profile-header-info-action button secondary dropdown-toggle"
                                            data-toggle="dropdown"
                                            style="background-color: #fab636 !important; width: 180px !important"
                                        >
                                            <span class="hide-text-mobile">Accept</span> Request
                                            <span class="caret"></span>
                                        </p>
                                        <ul class="dropdown-menu" style="padding: 10px !important; border-radius: 12px !important; text-align: left !important; margin-right: 18px !important">
                                        <li style="padding: 10px" @click="userfolloweracceptrequest(user_details.id)"><a href="javascript:void(0)">Accept Request</a></li>
                                        <li style="padding: 10px" @click="userfollowerdeclinerequest(user_details.id)"><a href="javascript:void(0)">Decline Request</a></li>
                                        </ul>
                                    </div>
                                    <p class="profile-header-info-action button primary"
                                        style="width: 180px !important">
                                        <span class="hide-text-mobile">Send</span>
                                        Message
                                    </p>
                                </div>

                                <!-- IF APP USER AND PROFILE OWNER DOES NOT HAVE ANY RELATIONSHIP; THEN SHOW FOLLOW BUTTON-->
                                <div class="profile-header-info-actions"
                                    v-if="!_isFriendWithUser(user_details.id) &&
                                    !_isPendingFollowingUser(user_details.id) &&
                                    !_isPendingFollowerUser(user_details.id) &&
                                    !_isFollowingUser(user_details.id) &&
                                    !_isSelf(user_details.id)"
                                    style="top: -38px !important"
                                >
                                    <p class="profile-header-info-action button secondary"
                                        style="background-color: #fab636 !important; width: 180px !important"
                                        @click="userfollowerfollowuser(user_details.id)"
                                    >
                                        <span class="hide-text-mobile">Follow</span>
                                    </p>
                                    <p class="profile-header-info-action button primary"
                                        style="width: 180px !important">
                                        <span class="hide-text-mobile">Send</span>
                                        Message
                                    </p>
                                </div>
                            </template>
                        </template>
                    </div>
                    <!-- /PROFILE HEADER INFO -->
                </div>

                <nav class="section-navigation" :class="app_panel_theme">
                    <div id="section-navigation-slider" class="section-menu">
                      <a class="section-menu-item" href="profile-about.html">
                        <svg class="section-menu-item-icon icon-profile">
                          <use xlink:href="#svg-profile"></use>
                        </svg>

                        <p class="section-menu-item-text" :class="app_text_theme">About Me</p>
                      </a>

                      <div @click="section_type = 'visitors'">
                        <a class="section-menu-item" :class="{'active': section_type == 'visitors'}" href="javascript:void(0)">
                            <svg class="section-menu-item-icon icon-magnifying-glass">
                              <use xlink:href="#svg-magnifying-glass"></use>
                            </svg>

                            <p class="section-menu-item-text" :class="app_text_theme">Profile Visits</p>
                        </a>
                      </div>

                      <div @click="section_type = 'followings'">
                        <a class="section-menu-item" :class="{'active': section_type == 'followings'}" href="javascript:void(0)" >
                            <svg class="section-menu-item-icon icon-friend">
                              <use xlink:href="#svg-friend"></use>
                            </svg>

                            <p class="section-menu-item-text" :class="app_text_theme">Followings</p>
                        </a>
                      </div>

                      <div @click="section_type = 'followers'">
                        <a class="section-menu-item" :class="{'active': section_type == 'followers'}" href="javascript:void(0)" >
                            <svg class="section-menu-item-icon icon-members">
                              <use xlink:href="#svg-members"></use>
                            </svg>

                            <p class="section-menu-item-text" :class="app_text_theme">Followers</p>
                        </a>
                      </div>

                      <div @click="section_type = 'posts'">
                        <a class="section-menu-item" :class="{'active': section_type == 'posts'}" href="javascript:void(0)">
                            <svg class="section-menu-item-icon icon-streams">
                              <use xlink:href="#svg-streams"></use>
                            </svg>

                            <p class="section-menu-item-text" :class="app_text_theme">Posts</p>
                        </a>
                      </div>

                      <a class="section-menu-item" href="profile-videos.html">
                        <svg class="section-menu-item-icon icon-videos">
                          <use xlink:href="#svg-videos"></use>
                        </svg>

                        <p class="section-menu-item-text" :class="app_text_theme">My Studio</p>
                      </a>

                      <a class="section-menu-item" href="profile-badges.html">
                        <svg class="section-menu-item-icon icon-blog-posts">
                          <use xlink:href="#svg-blog-posts"></use>
                        </svg>

                        <p class="section-menu-item-text" :class="app_text_theme">Blogs</p>
                      </a>
                    </div>

                    <div id="section-navigation-slider-controls" class="slider-controls">
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
                  </nav>

                <!-- POSTS SECTION -->
                <section class="section" v-if="section_type == 'posts'">
                    <div class="section-header" v-if="user_profile_post_list.length > 0">
                        <div class="section-header-info">
                            <p class="section-pretitle" v-if="user_details != null" :class="app_text_theme">Check @{{ user_details.name.split(' ')[0] }}'s</p>

                            <h2 class="section-title" :class="app_text_theme">Latest Compositions</h2>
                        </div>

                        <div class="section-header-actions" >
                            <template v-if="user_details != null">
                                <template v-if="app_user_details != null">
                                    <a class="section-header-action" href="/upload" v-if="user_details.id == app_user_details.id" :class="app_text_theme">Upload Track +</a>
                                </template>
                            </template>
                            <a class="section-header-action" href="profile-photos-inside.html" :class="app_text_theme">See All</a>
                        </div>
                    </div>
                    <div class="container" v-if="user_profile_post_list.length > 0"
                        style="padding: 0; max-width: 100% !important">
                        <div class="row">
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-6" style="margin-top: 24px" v-for="(post, pp) in user_profile_post_list" :key="pp"
                            @click="_loadPostPreview(post)">
                                <div class="video-box">
                                    <div class="video-box-cover">
                                        <figure class="video-box-cover-image liquid">
                                            <img :src="post.track_details[0].image_path != null ? s3_site + post.track_details[0].image_path : defaultThumbnail" alt="cover-08"
                                                style="border-top-left-radius: 12px !important; border-top-right-radius: 12px !important">
                                        </figure>

                                        <div class="play-button" style="top: 36px !important;
                                            height: 86px !important;
                                            width: 86px !important;
                                            padding: 0 !important;
                                            border: none !important;
                                            margin-left: -34px !important;
                                            background-color: #15151f00 !important"
                                        >
                                            <img src="{{ asset('assets/harmelo/logo/harmelo-logo-no-word-white.png') }}"
                                                height="80"
                                            >
                                        </div>
                                    </div>

                                    <div class="video-box-info" :class="app_panel_theme">
                                        <p class="video-box-title" :class="app_text_theme">@{{ post.track_details[0].title }}</p>

                                        <p class="video-box-text" :class="app_text_theme">@{{ moment(post.created_at).fromNow() }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="under-construction"
                        style="text-align: center; margin-top: 10px"
                        v-else
                    >
                        <h1 style="font-family: 'Titillium Web', sans-serif !important; font-size: 1rem; font-weight: 700 !important; color: #929096; margin-bottom: 30px"
                            v-if="user_details != null"
                        >
                            &#9839 @{{ user_details.name }} hasn't posted anything yet &#9835
                        </h1>
                        <template v-if="user_details != null">
                            <template v-if="app_user_details != null">
                                <a href="/upload"
                                    style="font-family: 'Titillium Web', sans-serif !important; font-size: 1rem; font-weight: 700 !important; color: #7e57c2"
                                    v-if="user_details.id == app_user_details.id"
                                >
                                    Upload Track +
                                </a>
                            </template>
                        </template>

                    </div>
                </section>
                <!-- /POSTS SECTION -->

                <!-- FOLLOWINGS SECTION -->
                <section class="section" v-if="section_type == 'followings'">
                    <div class="section-header">
                        <div class="section-header-info" v-if="user_details != null">
                            <p class="section-pretitle" :class="app_text_theme">Browse @{{ user_details.name }}</p>

                            <h2 class="section-title" :class="app_text_theme">Followings <span class="highlighted ml-3" :class="app_text_theme">@{{ user_profile_followings.length }}</span></h2>
                        </div>
                    </div>
                    <div class="grid grid-4-4-4">
                        <div class="user-preview" v-for="(user, uu) in user_profile_followings" :key="uu"
                            :class="app_panel_theme"
                        >
                            <figure class="user-preview-cover liquid" style="height: 119px !important">
                                <img :src="user.header_cover != null ?
                                    s3_site + 'public/user/' + user.id + '/cover/' + user.header_cover :
                                    s3_site + 'public/defaults/default-cover.jpg'" alt="cover-29"
                                    style="
                                    border-top-left-radius: 12px;
                                    border-top-right-radius: 12px;"
                                >
                            </figure>

                            <div class="user-preview-info">
                                <div class="user-short-description">
                                    <a class="user-short-description-avatar user-avatar no-stats"
                                        :href="'/user/' + user.slug"
                                        style="width: 103px !important; height: 108px !important;"
                                    >
                                        <div class="user-avatar-border">
                                            <div class="custom-avatar-tray" :class="app_panel_theme"
                                                style="width: 108px; height: 108px; border-radius: 50%;">
                                            </div>
                                        </div>

                                        <div
                                            class="user-avatar-content"
                                            :style="[user.avatar != null
                                                ? {'background':'transparent'}
                                                : {'background':'linear-gradient(150deg, rgb(76 37 204 / 69%), rgb(255 40 149 / 60%))'}]"
                                            style="
                                            border-radius: 50%;
                                            padding: 2px;
                                            left: 3px;
                                            top: 2px"
                                        >
                                            <img v-if="user.avatar != null"
                                                :src="_getProfileOwnerBackgroundImage(user, user.avatar)"
                                                style="border-radius: 50% !important;
                                                background-position: center !important;
                                                width: 98px !important;
                                                height: 98px !important"
                                            >
                                            <img v-else src="{{ asset('assets/harmelo/image/default_avatar.png') }}"
                                                style="border-radius: 50% !important;
                                                background-position: center !important;
                                                width: 98px !important;
                                                height: 98px !important"
                                            >
                                        </div>
                                    </a>

                                    <p class="user-short-description-title"><a href="profile-timeline.html" :class="app_text_theme">@{{ user.name }}</a></p>

                                    <p class="user-short-description-text" style="text-transform: none !important"><a href="javascript:void(0)" :class="app_text_theme">@{{ user.email }}</a></p>
                                </div>

                                <p class="user-preview-text" v-if="user.bio != null" :class="app_text_theme">@{{ user.bio }}</p>

                                <div class="user-preview-actions">
                                    <p class="button secondary">Add Friend +</p>
                                    <p class="button primary">Send Message</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION PAGER BAR -->
                    <div class="section-pager-bar" v-if="user_profile_followings.length > 6">
                        <div class="section-pager">
                            <div class="section-pager-item active">
                                <p class="section-pager-item-text">01</p>
                            </div>

                            <div class="section-pager-item">
                                <p class="section-pager-item-text">02</p>
                            </div>

                            <div class="section-pager-item">
                                <p class="section-pager-item-text">03</p>
                            </div>

                            <div class="section-pager-item">
                                <p class="section-pager-item-text">04</p>
                            </div>

                            <div class="section-pager-item">
                                <p class="section-pager-item-text">05</p>
                            </div>

                            <div class="section-pager-item">
                                <p class="section-pager-item-text">06</p>
                            </div>
                        </div>

                        <div class="section-pager-controls">
                            <div class="slider-control left disabled">
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
                    <!-- /SECTION PAGER BAR -->
                </section>
                <!-- /FOLLOWINGS SECTION -->

                <!-- FOLLOWERS SECTION -->
                <section class="section" v-if="section_type == 'followers'">
                    <div class="section-header">
                        <div class="section-header-info" v-if="user_details != null">
                            <p class="section-pretitle" :class="app_text_theme">Browse @{{ user_details.name }}</p>

                            <h2 class="section-title" :class="app_text_theme">Followers <span class="highlighted ml-3" :class="app_text_theme">@{{ user_profile_followers.length }}</span></h2>
                        </div>
                    </div>
                    <div class="grid grid-4-4-4">
                        <div class="user-preview" v-for="(user, uu) in user_profile_followers" :key="uu" :class="app_panel_theme">
                            <figure class="user-preview-cover liquid" style="height: 119px !important">
                                <img :src="user.header_cover != null ?
                                    s3_site + 'public/user/' + user.id + '/cover/' + user.header_cover :
                                    s3_site + 'public/defaults/default-cover.jpg'" alt="cover-29"
                                    style="
                                    border-top-left-radius: 12px;
                                    border-top-right-radius: 12px;"
                                >
                            </figure>

                            <div class="user-preview-info">
                                <div class="user-short-description">
                                    <a class="user-short-description-avatar user-avatar no-stats"
                                        :href="'/user/' + user.slug"
                                        style="width: 103px !important; height: 108px !important;"
                                    >
                                        <div class="user-avatar-border">
                                            <div class="custom-avatar-tray" :class="app_panel_theme"
                                                style="width: 108px; height: 108px; border-radius: 50%;">
                                            </div>
                                        </div>

                                        <div
                                            class="user-avatar-content"
                                            :style="[user.avatar != null
                                                ? {'background':'transparent'}
                                                : {'background':'linear-gradient(150deg, rgb(76 37 204 / 69%), rgb(255 40 149 / 60%))'}]"
                                            style="
                                            border-radius: 50%;
                                            padding: 2px;
                                            left: 3px;
                                            top: 2px"
                                        >
                                            <img v-if="user.avatar != null"
                                                :src="_getProfileOwnerBackgroundImage(user, user.avatar)"
                                                style="border-radius: 50% !important;
                                                background-position: center !important;
                                                width: 98px !important;
                                                height: 98px !important"
                                            >
                                            <img v-else src="{{ asset('assets/harmelo/image/default_avatar.png') }}"
                                                style="border-radius: 50% !important;
                                                background-position: center !important;
                                                width: 98px !important;
                                                height: 98px !important"
                                            >
                                        </div>
                                    </a>

                                    <p class="user-short-description-title"><a href="profile-timeline.html" :class="app_text_theme">@{{ user.name }}</a></p>

                                    <p class="user-short-description-text" style="text-transform: none !important"><a href="javascript:void(0)" :class="app_text_theme">@{{ user.email }}</a></p>
                                    <p class="user-short-description-text" style="text-transform: none !important" v-if="user.bio != null"><a href="javascript:void(0)" :class="app_text_theme">@{{ user.bio }}</a></p>
                                </div>

                                <div class="user-preview-actions">
                                    <p class="button secondary">Add Friend +</p>
                                    <p class="button primary">Send Message</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION PAGER BAR -->
                    <div class="section-pager-bar" v-if="user_profile_followers.length > 6">
                        <div class="section-pager">
                            <div class="section-pager-item active">
                                <p class="section-pager-item-text">01</p>
                            </div>

                            <div class="section-pager-item">
                                <p class="section-pager-item-text">02</p>
                            </div>

                            <div class="section-pager-item">
                                <p class="section-pager-item-text">03</p>
                            </div>

                            <div class="section-pager-item">
                                <p class="section-pager-item-text">04</p>
                            </div>

                            <div class="section-pager-item">
                                <p class="section-pager-item-text">05</p>
                            </div>

                            <div class="section-pager-item">
                                <p class="section-pager-item-text">06</p>
                            </div>
                        </div>

                        <div class="section-pager-controls">
                            <div class="slider-control left disabled">
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
                    <!-- /SECTION PAGER BAR -->
                </section>
                <!-- /FOLLOWERS SECTION -->

                <!-- VISITORS SECTION -->
                <section class="section" v-if="section_type == 'visitors'">
                    <div class="section-header">
                        <div class="section-header-info" v-if="user_details != null">
                            <p class="section-pretitle" :class="app_text_theme">Browse @{{ user_details.name }}</p>

                            <h2 class="section-title" :class="app_text_theme">Profile Visits <span class="highlighted ml-3" :class="app_text_theme">@{{ user_profile_visitors.length }}</span></h2>
                        </div>
                    </div>
                    <div class="grid grid-4-4-4">
                        <div class="user-preview" v-for="(user, uu) in user_profile_visitors" :key="uu" :class="app_panel_theme">
                            <figure class="user-preview-cover liquid" style="height: 119px !important">
                                <img :src="user.header_cover != null ?
                                    s3_site + 'public/user/' + user.id + '/cover/' + user.header_cover :
                                    s3_site + 'public/defaults/default-cover.jpg'" alt="cover-29"
                                    style="
                                    border-top-left-radius: 12px;
                                    border-top-right-radius: 12px;"
                                >
                            </figure>

                            <div class="user-preview-info">
                                <div class="user-short-description">
                                    <a class="user-short-description-avatar user-avatar no-stats"
                                        :href="'/user/' + user.slug"
                                        style="width: 103px !important; height: 108px !important;"
                                    >
                                        <div class="user-avatar-border">
                                            <div class="custom-avatar-tray" :class="app_panel_theme"
                                                style="width: 108px; height: 108px; border-radius: 50%;">
                                            </div>
                                        </div>

                                        <div
                                            class="user-avatar-content"
                                            :style="[user.avatar != null
                                                ? {'background':'transparent'}
                                                : {'background':'linear-gradient(150deg, rgb(76 37 204 / 69%), rgb(255 40 149 / 60%))'}]"
                                            style="
                                            border-radius: 50%;
                                            padding: 2px;
                                            left: 3px;
                                            top: 2px"
                                        >
                                            <img v-if="user.avatar != null"
                                                :src="_getProfileOwnerBackgroundImage(user, user.avatar)"
                                                style="border-radius: 50% !important;
                                                background-position: center !important;
                                                width: 98px !important;
                                                height: 98px !important"
                                            >
                                            <img v-else src="{{ asset('assets/harmelo/image/default_avatar.png') }}"
                                                style="border-radius: 50% !important;
                                                background-position: center !important;
                                                width: 98px !important;
                                                height: 98px !important"
                                            >
                                        </div>
                                    </a>

                                    <p class="user-short-description-title"><a href="profile-timeline.html" :class="app_text_theme">@{{ user.name }}</a></p>

                                    <p class="user-short-description-text" style="text-transform: none !important"><a href="javascript:void(0)" :class="app_text_theme">@{{ user.email }}</a></p>
                                    <p class="user-short-description-text" style="text-transform: none !important" v-if="user.bio != null"><a href="javascript:void(0)" :class="app_text_theme">@{{ user.bio }}</a></p>
                                </div>

                                <div class="user-preview-actions">
                                    <p class="button secondary">Add Friend +</p>
                                    <p class="button primary">Send Message</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION PAGER BAR -->
                    <div class="section-pager-bar" v-if="user_profile_visitors.length > 6">
                        <div class="section-pager">
                            <div class="section-pager-item active">
                                <p class="section-pager-item-text">01</p>
                            </div>

                            <div class="section-pager-item">
                                <p class="section-pager-item-text">02</p>
                            </div>

                            <div class="section-pager-item">
                                <p class="section-pager-item-text">03</p>
                            </div>

                            <div class="section-pager-item">
                                <p class="section-pager-item-text">04</p>
                            </div>

                            <div class="section-pager-item">
                                <p class="section-pager-item-text">05</p>
                            </div>

                            <div class="section-pager-item">
                                <p class="section-pager-item-text">06</p>
                            </div>
                        </div>

                        <div class="section-pager-controls">
                            <div class="slider-control left disabled">
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
                    <!-- /SECTION PAGER BAR -->
                </section>
                <!-- /VISITORS SECTION -->
            </div>
            <!-- /CONTENT GRID -->
        </div>
    </profile>
    <link href="{{ asset('assets/harmelo/css/newsfeed.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/harmelo/css/profile.css') }}" rel="stylesheet">
@endsection

@section('page_stylesheets')

@endsection
