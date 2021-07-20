@extends('layouts.layout-app-admin')
@section('content')
    <profile-settings inline-template>
        <div>
            <!-- CONTENT GRID -->
            <div class="content-grid">

                <!-- GRID -->
                <div class="grid grid-3-9 medium-space">
                <!-- GRID COLUMN -->
                <div class="account-hub-sidebar" v-if="user_details != null">
                    <!-- SIDEBAR BOX -->
                    <div class="sidebar-box no-padding" :class="app_panel_theme">
                    <!-- SIDEBAR MENU -->
                    <div class="sidebar-menu">
                        <!-- SIDEBAR MENU ITEM -->
                        <div class="sidebar-menu-item" :class="app_borderbot_theme">
                        <!-- SIDEBAR MENU HEADER -->
                        <div class="sidebar-menu-header accordion-trigger-linked" :class="app_panel_theme">
                            <!-- SIDEBAR MENU HEADER ICON -->
                            <svg class="sidebar-menu-header-icon icon-profile">
                            <use xlink:href="#svg-profile"></use>
                            </svg>
                            <!-- /SIDEBAR MENU HEADER ICON -->

                            <!-- SIDEBAR MENU HEADER CONTROL ICON -->
                            <div class="sidebar-menu-header-control-icon">
                            <!-- SIDEBAR MENU HEADER CONTROL ICON OPEN -->
                            <svg class="sidebar-menu-header-control-icon-open icon-minus-small">
                                <use xlink:href="#svg-minus-small"></use>
                            </svg>
                            <!-- /SIDEBAR MENU HEADER CONTROL ICON OPEN -->

                            <!-- SIDEBAR MENU HEADER CONTROL ICON CLOSED -->
                            <svg class="sidebar-menu-header-control-icon-closed icon-plus-small">
                                <use xlink:href="#svg-plus-small"></use>
                            </svg>
                            <!-- /SIDEBAR MENU HEADER CONTROL ICON CLOSED -->
                            </div>
                            <!-- /SIDEBAR MENU HEADER CONTROL ICON -->

                            <!-- SIDEBAR MENU HEADER TITLE -->
                            <p class="sidebar-menu-header-title" :class="app_text_theme">My Profile</p>
                            <!-- /SIDEBAR MENU HEADER TITLE -->

                            <!-- SIDEBAR MENU HEADER TEXT -->
                            <p class="sidebar-menu-header-text" :class="app_text_theme">Change your basic information, avatar &amp; cover.</p>
                            <!-- /SIDEBAR MENU HEADER TEXT -->
                        </div>
                        <!-- /SIDEBAR MENU HEADER -->

                        <!-- SIDEBAR MENU BODY -->
                        <div class="sidebar-menu-body accordion-content-linked accordion-open" :class="[app_panel_theme, app_bordertop_theme]">
                            <!-- SIDEBAR MENU LINK -->
                            <a class="sidebar-menu-link active" href="hub-profile-info.html" :class="app_text_theme">Profile Info</a>
                            <!-- /SIDEBAR MENU LINK -->

                            <!-- SIDEBAR MENU LINK -->
                            <a class="sidebar-menu-link" href="javascript:void(0)"
                                v-if="user_details.user_type === 'teacher'"
                                :class="app_text_theme"
                            >
                                Instructor Profile
                            </a>
                            <!-- /SIDEBAR MENU LINK -->

                            <!-- SIDEBAR MENU LINK -->
                            <a class="sidebar-menu-link" href="hub-profile-messages.html" :class="app_text_theme">Messages</a>
                            <!-- /SIDEBAR MENU LINK -->

                            <!-- SIDEBAR MENU LINK -->
                            <a class="sidebar-menu-link" href="hub-profile-requests.html" :class="app_text_theme">Friend Requests</a>
                            <!-- /SIDEBAR MENU LINK -->
                        </div>
                        <!-- /SIDEBAR MENU BODY -->
                        </div>
                        <!-- /SIDEBAR MENU ITEM -->

                        <!-- SIDEBAR MENU ITEM -->
                        <div class="sidebar-menu-item" :class="app_borderbot_theme">
                        <!-- SIDEBAR MENU HEADER -->
                        <div class="sidebar-menu-header accordion-trigger-linked" :class="app_panel_theme">
                            <!-- SIDEBAR MENU HEADER ICON -->
                            <svg class="sidebar-menu-header-icon icon-settings">
                            <use xlink:href="#svg-settings"></use>
                            </svg>
                            <!-- /SIDEBAR MENU HEADER ICON -->

                            <!-- SIDEBAR MENU HEADER CONTROL ICON -->
                            <div class="sidebar-menu-header-control-icon">
                            <!-- SIDEBAR MENU HEADER CONTROL ICON OPEN -->
                            <svg class="sidebar-menu-header-control-icon-open icon-minus-small">
                                <use xlink:href="#svg-minus-small"></use>
                            </svg>
                            <!-- /SIDEBAR MENU HEADER CONTROL ICON OPEN -->

                            <!-- SIDEBAR MENU HEADER CONTROL ICON CLOSED -->
                            <svg class="sidebar-menu-header-control-icon-closed icon-plus-small">
                                <use xlink:href="#svg-plus-small"></use>
                            </svg>
                            <!-- /SIDEBAR MENU HEADER CONTROL ICON CLOSED -->
                            </div>
                            <!-- /SIDEBAR MENU HEADER CONTROL ICON -->

                            <!-- SIDEBAR MENU HEADER TITLE -->
                            <p class="sidebar-menu-header-title" :class="app_text_theme">Account</p>
                            <!-- /SIDEBAR MENU HEADER TITLE -->

                            <!-- SIDEBAR MENU HEADER TEXT -->
                            <p class="sidebar-menu-header-text" :class="app_text_theme">Change settings, configure notifications, and review your privacy</p>
                            <!-- /SIDEBAR MENU HEADER TEXT -->
                        </div>
                        <!-- /SIDEBAR MENU HEADER -->

                        <!-- SIDEBAR MENU BODY -->
                        <div class="sidebar-menu-body accordion-content-linked" :class="[app_panel_theme, app_bordertop_theme]">
                            <!-- SIDEBAR MENU LINK -->
                            <a class="sidebar-menu-link" href="hub-account-info.html" :class="app_text_theme">Account Info</a>
                            <!-- /SIDEBAR MENU LINK -->

                            <!-- SIDEBAR MENU LINK -->
                            <a class="sidebar-menu-link" href="hub-account-password.html" :class="app_text_theme">Change Password</a>
                            <!-- /SIDEBAR MENU LINK -->

                            <!-- SIDEBAR MENU LINK -->
                            <a class="sidebar-menu-link" href="hub-account-settings.html" :class="app_text_theme">Privacy Settings</a>
                            <!-- /SIDEBAR MENU LINK -->
                        </div>
                        <!-- /SIDEBAR MENU BODY -->
                        </div>
                        <!-- /SIDEBAR MENU ITEM -->

                        <!-- SIDEBAR MENU ITEM -->
                        <div class="sidebar-menu-item" :class="app_borderbot_theme">
                        <!-- SIDEBAR MENU HEADER -->
                        <div class="sidebar-menu-header accordion-trigger-linked" :class="app_panel_theme">
                            <!-- SIDEBAR MENU HEADER ICON -->
                            <svg class="sidebar-menu-header-icon icon-group">
                            <use xlink:href="#svg-group"></use>
                            </svg>
                            <!-- /SIDEBAR MENU HEADER ICON -->

                            <!-- SIDEBAR MENU HEADER CONTROL ICON -->
                            <div class="sidebar-menu-header-control-icon">
                            <!-- SIDEBAR MENU HEADER CONTROL ICON OPEN -->
                            <svg class="sidebar-menu-header-control-icon-open icon-minus-small">
                                <use xlink:href="#svg-minus-small"></use>
                            </svg>
                            <!-- /SIDEBAR MENU HEADER CONTROL ICON OPEN -->

                            <!-- SIDEBAR MENU HEADER CONTROL ICON CLOSED -->
                            <svg class="sidebar-menu-header-control-icon-closed icon-plus-small">
                                <use xlink:href="#svg-plus-small"></use>
                            </svg>
                            <!-- /SIDEBAR MENU HEADER CONTROL ICON CLOSED -->
                            </div>
                            <!-- /SIDEBAR MENU HEADER CONTROL ICON -->

                            <!-- SIDEBAR MENU HEADER TITLE -->
                            <p class="sidebar-menu-header-title" :class="app_text_theme">Groups</p>
                            <!-- /SIDEBAR MENU HEADER TITLE -->

                            <!-- SIDEBAR MENU HEADER TEXT -->
                            <p class="sidebar-menu-header-text" :class="app_text_theme">Create new groups, manage the ones you created or accept invites!</p>
                            <!-- /SIDEBAR MENU HEADER TEXT -->
                        </div>
                        <!-- /SIDEBAR MENU HEADER -->

                        <!-- SIDEBAR MENU BODY -->
                        <div class="sidebar-menu-body accordion-content-linked" :class="[app_panel_theme, app_bordertop_theme]">
                            <!-- SIDEBAR MENU LINK -->
                            <a class="sidebar-menu-link" href="hub-group-management.html" :class="app_text_theme">Manage Groups</a>
                            <!-- /SIDEBAR MENU LINK -->

                            <!-- SIDEBAR MENU LINK -->
                            <a class="sidebar-menu-link" href="hub-group-invitations.html" :class="app_text_theme">Invitations</a>
                            <!-- /SIDEBAR MENU LINK -->
                        </div>
                        <!-- /SIDEBAR MENU BODY -->
                        </div>
                        <!-- /SIDEBAR MENU ITEM -->

                        <!-- SIDEBAR MENU ITEM -->
                        <div class="sidebar-menu-item" :class="app_borderbot_theme">
                        <!-- SIDEBAR MENU HEADER -->
                        <div class="sidebar-menu-header accordion-trigger-linked" :class="app_panel_theme">
                            <!-- SIDEBAR MENU HEADER ICON -->
                            <svg class="sidebar-menu-header-icon icon-store">
                            <use xlink:href="#svg-store"></use>
                            </svg>
                            <!-- /SIDEBAR MENU HEADER ICON -->

                            <!-- SIDEBAR MENU HEADER CONTROL ICON -->
                            <div class="sidebar-menu-header-control-icon">
                            <!-- SIDEBAR MENU HEADER CONTROL ICON OPEN -->
                            <svg class="sidebar-menu-header-control-icon-open icon-minus-small">
                                <use xlink:href="#svg-minus-small"></use>
                            </svg>
                            <!-- /SIDEBAR MENU HEADER CONTROL ICON OPEN -->

                            <!-- SIDEBAR MENU HEADER CONTROL ICON CLOSED -->
                            <svg class="sidebar-menu-header-control-icon-closed icon-plus-small">
                                <use xlink:href="#svg-plus-small"></use>
                            </svg>
                            <!-- /SIDEBAR MENU HEADER CONTROL ICON CLOSED -->
                            </div>
                            <!-- /SIDEBAR MENU HEADER CONTROL ICON -->

                            <!-- SIDEBAR MENU HEADER TITLE -->
                            <p class="sidebar-menu-header-title" :class="app_text_theme">My Store</p>
                            <!-- /SIDEBAR MENU HEADER TITLE -->

                            <!-- SIDEBAR MENU HEADER TEXT -->
                            <p class="sidebar-menu-header-text" :class="app_text_theme">Review your account, manage products check stats and much more!</p>
                            <!-- /SIDEBAR MENU HEADER TEXT -->
                        </div>
                        <!-- /SIDEBAR MENU HEADER -->

                        <!-- SIDEBAR MENU BODY -->
                        <div class="sidebar-menu-body accordion-content-linked" :class="[app_panel_theme, app_bordertop_theme]">
                            <!-- SIDEBAR MENU LINK -->
                            <a class="sidebar-menu-link" href="hub-store-account.html" :class="app_text_theme">My Account</a>
                            <!-- /SIDEBAR MENU LINK -->

                            <!-- SIDEBAR MENU LINK -->
                            <a class="sidebar-menu-link" href="hub-store-statement.html" :class="app_text_theme">Sales Statement</a>
                            <!-- /SIDEBAR MENU LINK -->

                            <!-- SIDEBAR MENU LINK -->
                            <a class="sidebar-menu-link" href="hub-store-items.html" :class="app_text_theme">Manage Items</a>
                            <!-- /SIDEBAR MENU LINK -->

                            <!-- SIDEBAR MENU LINK -->
                            <a class="sidebar-menu-link" href="hub-store-downloads.html" :class="app_text_theme">Downloads</a>
                            <!-- /SIDEBAR MENU LINK -->
                        </div>
                        <!-- /SIDEBAR MENU BODY -->
                        </div>
                        <!-- /SIDEBAR MENU ITEM -->
                    </div>
                    <!-- /SIDEBAR MENU -->
                    </div>
                    <!-- /SIDEBAR BOX -->
                </div>
                <!-- /GRID COLUMN -->

                <div class="account-hub-content">

                    <!-- GRID COLUMN -->
                    <div class="grid-column">
                    <!-- GRID -->
                    <div class="progress" style="height: 5px; margin-right: 5px; margin-left: 5px; border-radius: 0 !important" v-if="editProfile_uploadProgress != 0 && editProfile_uploadProgress != 100">
                        <div class="progress-bar-purple progress-bar-striped progress-bar-animated"
                            role="progressbar"
                            :style="{'width': editProfile_uploadProgress + '%'}"
                            :aria-valuenow="editProfile_uploadProgress"
                            aria-valuemin="0"
                            aria-valuemax="100">
                        </div>
                    </div>
                    <!--<div class="grid grid-half centered">
                        <div class="upload-box" @click="panel_type = false">
                            <svg class="upload-box-icon icon-members">
                                <use xlink:href="#svg-members"></use>
                            </svg>
                            <p class="upload-box-title">Basic Information</p>
                        </div>

                        <div class="upload-box" @click="panel_type = true">
                            <svg class="upload-box-icon icon-members">
                                <use xlink:href="#svg-members"></use>
                            </svg>
                            <p class="upload-box-title">Avatar & Cover Picture</p>
                        </div>
                    </div> -->
                    <!-- /GRID -->

                    <div style="display: contents" :class="{'activePanel':panel_type}">
                        <template v-if="!panel_type">
                            <!-- WIDGET BOX -->
                            <div class="widget-box my_profile_box__basincInfo" :class="app_panel_theme" v-if="user_details != null">
                                <!-- WIDGET BOX TITLE -->
                                <p class="widget-box-title" :class="app_text_theme">Basic Information</p>
                                <!-- /WIDGET BOX TITLE -->

                                <!-- WIDGET BOX CONTENT -->
                                <div class="widget-box-content">
                                <!-- FORM -->
                                <form class="form">
                                    <div class="form-row split">

                                        <div class="form-item">
                                            <div v-if="user_details.avatar != null"
                                                class="cropper-preview"
                                                style="
                                                border-top-left-radius: 12px;
                                                border-top-right-radius: 12px;
                                                height: 397px;
                                                width: 397px;
                                                background: #161b28;
                                                margin: auto !important">
                                                <div class="c"
                                                    style="
                                                    height: 100%; width: 100%;display: flex;
                                                    align-items: center;
                                                    justify-content: center;"
                                                >
                                                    <img v-if="!avatar_edit_mode"
                                                        @click="_triggerFileInput('avatar_photo_fileinput')"
                                                        style="
                                                        border-top-left-radius: 12px;
                                                        border-top-right-radius: 12px;
                                                        cursor: pointer;
                                                        background-position: center;
                                                        background-size: containt;
                                                        height: 100%;
                                                        width: 100%"
                                                        :src="_getBackgroundImage(user_details.avatar)"/>
                                                    <cropper v-if="avatar_edit_mode"
                                                        ref="avatarcropper"
                                                        :src="avatar_img_rc"
                                                        :stencil-size="{
                                                            width: 283,
                                                            height: 283
                                                        }"
                                                        :stencil-props="{
                                                            handlers: {},
                                                            movable: true,
                                                            resizable: false,
                                                        }"
                                                        stencil-component="circle-stencil"
                                                        image-restriction="fit-area"
                                                    />
                                                </div>
                                                <div class="action-buttons"
                                                    style="width: 100%; display: flex; margin-top: 5px !important">
                                                    <p class="profile-header-info-action button secondary"
                                                        :class="{'mr-1': avatar_edit_mode}"
                                                        :style="[!avatar_edit_mode ? {'width':'100%', 'border-bottom-right-radius':'12px !important'} : {'width':'50%'}]"
                                                        style="
                                                        background-color: #2b3345 !important;
                                                        height: 40px;
                                                        line-height: 38px;
                                                        box-shadow: none !important;
                                                        border-radius: 0;
                                                        border-bottom-left-radius: 12px !important"
                                                        @click="_triggerFileInput('avatar_photo_fileinput')"
                                                    >
                                                        <span class="hide-text-mobile">Select image</span>
                                                    </p>
                                                    <p
                                                        v-if="avatar_edit_mode"
                                                        class="profile-header-info-action button secondary"
                                                        :class="{'ml-1': avatar_edit_mode}"
                                                        @click="_discardCoverChange()"
                                                        style="
                                                        width: 50%;
                                                        background-color: #2b3345 !important;
                                                        height: 40px;
                                                        line-height: 38px;
                                                        box-shadow: none !important;
                                                        border-radius: 0;
                                                        border-bottom-right-radius: 12px !important"
                                                    >
                                                        <span class="hide-text-mobile">Cancel</span>
                                                    </p>
                                                </div>
                                            </div>
                                            <input
                                                :disabled="is_uploading_avatar"
                                                type="file"
                                                id="avatar_photo_fileinput"
                                                accept="image/*"
                                                style="display: none"
                                                @change="loadImage($event)"
                                                @click="resetAvatarInputValue($event)"
                                            >
                                        </div>

                                        <div class="form-item">
                                            <div class="form-row name-field__form-row">
                                                <div class="form-item">
                                                    <div class="form-input small active">
                                                        <label for="profile-name" :class="[app_panel_theme, app_text_theme]">Name</label>
                                                        <input type="text" id="profile-name" name="profile_name" v-model="new_name" :class="[app_panel_theme, app_text_theme]">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-row margin-y-38">
                                                <div class="form-item">
                                                    <div class="form-input small active">
                                                        <label for="profile-tagline" :class="[app_panel_theme, app_text_theme]">Username</label>
                                                        <input type="text" id="profile-tagline" name="profile_tagline" v-model="new_slug" :class="[app_panel_theme, app_text_theme]">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-row margin-y-38">
                                                <div class="form-item">
                                                    <div class="form-input small active">
                                                        <label for="profile-public-website" :class="[app_panel_theme, app_text_theme]"> Website</label>
                                                        <input type="text" id="profile-public-website" name="new_website" v-model="new_website" :class="[app_panel_theme, app_text_theme]">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-row margin-y-38">
                                                <div class="form-item">
                                                    <div class="form-input small active">
                                                        <label for="profile-occupation" :class="[app_panel_theme, app_text_theme]">Occupation</label>
                                                        <input type="text" id="profile-occupation" name="profile_occupation" v-model="new_occupation" :class="[app_panel_theme, app_text_theme]">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-row margin-t-38">
                                                <div class="form-select">
                                                    <label for="profile-country" :class="[app_panel_theme, app_text_theme]">Country</label>
                                                    <input type="text" id="profile-country" name="profile_country" v-model="new_country" :class="[app_panel_theme, app_text_theme]">
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="form-row split">
                                        <div class="form-item margin-t-38">
                                            <div class="form-select">
                                                <label for="profile-city" :class="[app_panel_theme, app_text_theme]">City</label>
                                                <input type="text" id="profile-city" name="profile_city" v-model="new_city" :class="[app_panel_theme, app_text_theme]">
                                            </div>
                                        </div>

                                        <div class="form-item">
                                            <div class="form-input small full" style="height: 160px !important">
                                                <textarea id="profile-description" name="profile_description" placeholder="Write a little description about you..." v-model="new_bio"
                                                    :class="[app_panel_theme, app_text_theme]"
                                                ></textarea>
                                                <div class="dropleft emoji-invoker" >
                                                    <div data-toggle="dropdown">
                                                        <svg height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M0 0h24v24H0z" fill="none"/>
                                                            <path v-if="current_app_theme === 'dark'" fill="#fff" d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                                                            <path v-if="current_app_theme === 'light'" fill="#1d2333" d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                                                        </svg>
                                                    </div>
                                                    <ul class="dropdown-menu" @click="_dropdownMenuClicked($event)">
                                                        <vemojipicker  @select="selectEmoji" />
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <!-- /FORM -->
                                </div>
                                <!-- WIDGET BOX CONTENT -->
                            </div>
                            <!-- /WIDGET BOX -->

                            
                            <div class="widget-box my_profile_box__basincInfo" :class="app_panel_theme">

                                <p class="widget-box-title" :class="app_text_theme">Manage Instruments</p>

                                <div class="widget-box-content" v-for="(category, cc) in categoried_instrument_list" :key="cc">
                                    <p class="text-white pb-4">@{{ category.category_name }}</p>
                                    <div class="draggable-items" style="margin-bottom: 15px; justify-content: normal !important">
                                        <template v-for="(instrument, ii) in category.instruments">
                                            <div :key="ii" class="draggable-item item_instrument" :class="{'picked_instrument': _isInstrumentPicked(instrument.id, category.id)}"
                                                @click="!_isInstrumentPicked(instrument.id, category.id) ? _selectInstrument(instrument) : _deselectInstrument(instrument)"
                                            >
                                                <div class="badge-item">
                                                    <img :src="s3_site + 'public/instruments/' + instrument.instrument_name + '.png'" alt="badge-goldc-s"
                                                        style="width: 38px;
                                                        height: 37px;"
                                                    >
                                                </div>
                                            </div>
                                        </template>
                                        <div class="draggable-item empty"></div>
                                    </div>
                                </div>
                            </div>

                            <form class="form">
                                <div class="form-row">
                                    <div class="form-item text-center">
                                        <p class="button primary" v-if="is_updating_profile && !is_uploading_avatar && !is_uploading_cover"
                                            style="box-shadow: none !important;
                                            background: rgb(97 93 250);
                                            width: 100%;
                                            height: 64px !important;
                                            font-size: 1.35rem !important;
                                            line-height: 60px !important;"
                                        >
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            Loading...
                                        </p> 
                                        <p class="button primary" v-else
                                            style="box-shadow: none !important;
                                            background: rgb(97 93 250);
                                            width: 100%;
                                            height: 64px !important;
                                            font-size: 1.35rem !important;
                                            line-height: 60px !important;"
                                            @click="userprofileeditprofile()"
                                        >
                                            Save Changes
                                        </p>
                                    </div>
                                </div>
                            </form>
                            
                        </template>

                        <template v-else>
                            <div class="widget-box my_profile_box__avatar" :class="app_panel_theme">
                                <!-- WIDGET BOX TITLE -->
                                <p class="widget-box-title">Change Avatar</p>
                                <!-- /WIDGET BOX TITLE -->

                                <!-- WIDGET BOX CONTENT -->
                                <div class="widget-box-content">
                                    <form class="form">
                                        <div class="form-row split">
                                            <div class="form-item">
                                                <div class="progress" style="height: 5px; margin-right: 5px; margin-left: 5px; border-radius: 0 !important" v-if="avatar_uploadProgress != 0 && avatar_uploadProgress != 100">
                                                    <div class="progress-bar-purple progress-bar-striped progress-bar-animated"
                                                        role="progressbar"
                                                        :style="{'width': avatar_uploadProgress + '%'}"
                                                        :aria-valuenow="avatar_uploadProgress"
                                                        aria-valuemin="0"
                                                        aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <croppa v-model="avatar_croppa" :prevent-white-space="true" :auto-sizing="true" :width="428" :height="318" @new-image-drawn="avatar_onNewImage" @zoom="avatar_onZoom" @loading-end="avatar_imageChanged()"
                                                @file-choose="avatar_handleCroppaFileChoose" placeholder="Select a profile picture" placeholder-color="#fff" :placeholder-font-size="13"
                                                    canvas-color="transparent"
                                                    :show-remove-button="true"
                                                    remove-button-color="black"
                                                    accept=".jpeg,.jpg,.png"
                                                    @file-type-mismatch="onFileTypeMismatch"
                                                    style="background-color: #3f249c;
                                                    border: 2px solid #3f485f;
                                                    border-radius: 8px;
                                                    cursor: pointer;
                                                    text-align: center"
                                                    class="__avatar-croppa"
                                                >
                                                    <!-- IF USER IS REGISTERED FROM GOOGLE/FACEBOOK, Do not include s3_site variable, else, include -->
                                                    <img crossOrigin="anonymous" :src="user_details.provider_id == null ? s3_site+user_details.avatar : user_details.avatar"
                                                        slot="initial" style="width: 428.023px;height: 321.984px; object-fit: contain"
                                                    >
                                                </croppa>
                                                <input type="range" @input="avatar_onSliderChange" :min="avatar_sliderMin" :max="avatar_sliderMax" step=".001" v-model="avatar_sliderVal" style="width: 100% !important">
                                                <img :src="avatarDataUrl" style="display: none">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!-- WIDGET BOX CONTENT -->
                            </div>
                            <div class="widget-box my_profile_box__avatar" :class="app_panel_theme">
                                <!-- WIDGET BOX TITLE -->
                                <p class="widget-box-title">Change Cover</p>
                                <!-- /WIDGET BOX TITLE -->

                                <!-- WIDGET BOX CONTENT -->
                                <div class="widget-box-content">
                                    <form class="form">
                                        <div class="form-row split">
                                            <div class="form-item">
                                                <div class="progress" style="height: 5px; margin-right: 5px; margin-left: 5px; border-radius: 0 !important" v-if="cover_uploadProgress != 0 && cover_uploadProgress != 100">
                                                    <div class="progress-bar-purple progress-bar-striped progress-bar-animated"
                                                        role="progressbar"
                                                        :style="{'width': cover_uploadProgress + '%'}"
                                                        :aria-valuenow="cover_uploadProgress"
                                                        aria-valuemin="0"
                                                        aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <croppa v-model="cover_croppa" :prevent-white-space="true" :auto-sizing="true" :height="318" @new-image-drawn="cover_onNewImage" @zoom="cover_onZoom" @loading-end="cover_imageChanged()"
                                                @file-choose="cover_handleCroppaFileChoose" placeholder="Select a cover photo" placeholder-color="#fff" :placeholder-font-size="13"
                                                    canvas-color="transparent"
                                                    :show-remove-button="true"
                                                    remove-button-color="black"
                                                    accept=".jpeg,.jpg,.png"
                                                    @file-type-mismatch="onFileTypeMismatch"

                                                    style="background-color: #3f249c;
                                                    border: 2px solid #3f485f;
                                                    border-radius: 8px;
                                                    cursor: pointer;
                                                    width: 100%;
                                                    text-align: center"
                                                    >
                                                </croppa>
                                                <input type="range" @input="cover_onSliderChange" :min="cover_sliderMin" :max="cover_sliderMax" step=".001" v-model="cover_sliderVal" style="width: 100% !important">
                                                <img :src="avatarDataUrl" style="display: none">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!-- WIDGET BOX CONTENT -->
                            </div>
                        </template>
                        <!-- /WIDGET BOX -->
                    </div>

                    </div>
                    <!-- /GRID COLUMN -->
                </div>

                
                </div>
                <!-- /GRID -->
            </div>
            <!-- /CONTENT GRID -->
        </div>
    </profile-settings>
    <link href="{{ asset('assets/harmelo/css/profilesettings.css') }}" rel="stylesheet">
@endsection

@section('page_stylesheets')

@endsection
