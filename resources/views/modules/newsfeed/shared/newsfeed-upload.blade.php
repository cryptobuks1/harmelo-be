@extends('layouts.layout-app-admin')
@section('content')
<newsfeedupload inline-template>
<div class="main-body">
    <!-- CONTENT GRID -->
  <div class="content-grid">

    <!-- GRID -->
    <div class="grid centered"
    >

      <!-- GRID COLUMN -->
      <div class="account-hub-content">

        <!-- GRID COLUMN -->
        <div class="grid-column">
            <!-- WIDGET BOX - AUDIOS-->
          <div class="widget-box" :class="app_panel_theme">
            <!-- WIDGET BOX TITLE -->
            <p class="widget-box-title" :class="app_text_theme">Track</p>
            <!-- /WIDGET BOX TITLE -->

            <!-- WIDGET BOX CONTENT -->
            <div class="widget-box-content">
              <!-- FORM -->
              <form class="form">
                <!-- FORM ROW -->
                <div class="form-row split">
                    <!-- FORM ITEM -->
                    <div class="form-item">
                        <vue-file-agent
                            ref="vueFileAgent"
                            :theme="'list'"
                            :multiple="false"
                            :deletable="true"
                            :meta="true"
                            :accept="'.mp3,.wav'"
                            :maxSize="'20MB'"
                            :maxFiles="30"
                            :helpText="'Drag & drop or select your tracks and albums here'"
                            :errorText="{
                                type: 'Invalid file type. Only .MP3 and .WAV formats are allowed',
                                size: 'Files should not exceed 20MB in size',
                            }"
                            @select="filesSelected($event)"
                            @beforedelete="onBeforeDelete($event)"
                            @delete="fileDeleted($event)"
                            v-model="fileRecords"
                        ></vue-file-agent>
                    </div>
                    <!-- /FORM ITEM -->
                  </div>
                  <!-- /FORM ROW -->
              </form>
              <!-- /FORM -->
            </div>
            <!-- WIDGET BOX CONTENT -->
          </div>
          <!-- /WIDGET BOX - AUDIOS -->

          <!-- WIDGET BOX - BASIC DETAILS-->
          <div class="widget-box" :class="app_panel_theme">
            <!-- WIDGET BOX TITLE -->
            <p class="widget-box-title" :class="app_text_theme">Basic Details</p>
            <!-- /WIDGET BOX TITLE -->

            <!-- WIDGET BOX CONTENT -->
            <div class="widget-box-content">
              <!-- FORM -->
              <form class="form" style="margin-bottom: 35px">
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
                                    <img v-if="!album_art_edit_mode"
                                        @click="_triggerFileInput('album_art_fileinput')"
                                        style="
                                        border-top-left-radius: 12px;
                                        border-top-right-radius: 12px;
                                        cursor: pointer;
                                        background-position: center;
                                        background-size: containt;
                                        height: 100%;
                                        width: 100%"
                                        :src="defaultThumbnail"/>
                                    <cropper v-if="album_art_edit_mode"
                                        ref="albumartcropper"
                                        :src="album_art_img_rc"
                                        :stencil-size="{
                                            width: 600,
                                            height: 416
                                        }"
                                        :stencil-props="{
                                            handlers: {},
                                            movable: true,
                                            resizable: false,
                                        }"
                                        image-restriction="fill-area"
                                    />
                                </div>
                                <div class="action-buttons"
                                    style="width: 100%; display: flex; margin-top: 5px !important">
                                    <p class="profile-header-info-action button secondary"
                                        :class="{'mr-1': album_art_edit_mode}"
                                        :style="[!album_art_edit_mode ? {'width':'100%', 'border-bottom-right-radius':'12px !important'} : {'width':'50%'}]"
                                        style="background-color: #2b3345 !important;
                                        height: 40px;
                                        line-height: 38px;
                                        box-shadow: none !important;
                                        border-radius: 0;
                                        border-bottom-left-radius: 12px !important"
                                        @click="_triggerFileInput('album_art_fileinput')"
                                    >
                                        <span class="hide-text-mobile">Select image</span>
                                    </p>
                                    <p
                                        v-if="album_art_edit_mode"
                                        class="profile-header-info-action button secondary"
                                        :class="{'ml-1': album_art_edit_mode}"
                                        @click="_discardAlbumArtChange()"
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
                                type="file"
                                id="album_art_fileinput"
                                accept="image/*"
                                style="display: none"
                                @change="loadImage($event)"
                                @click="resetAlbumArtInputValue($event)"
                            >
                      </div>

                        <div class="form-item">
                            <!-- TITLE -->
                            <div class="form-row title-field__form-row">
                                <div class="form-item">
                                    <!--<v-app>
                                        <v-text-field v-bind:id="app_input_fields_theme" data-app
                                            dense outlined 
                                            placeholder="Title" 
                                            v-model="track_title" 
                                            prepend-inner-icon="article"></v-text-field>
                                    </v-app>-->
                                    <div class="form-input small active">
                                        <label for="track-title" :class="[app_text_theme, app_panel_theme]">Title</label>
                                        <input type="text" id="track-title" name="track_title" v-model="track_title" :class="[app_panel_theme, app_text_theme]">
                                    </div>
                                </div>
                            </div>

                            <!-- TRACK SLUG -->
                            <div class="form-row margin-y-38">
                                <div class="form-item">
                                    <div class="form-input small active">
                                        <label for="track-producer" :class="[app_text_theme, app_panel_theme]">Producer</label>
                                        <input type="text" id="track-producer" name="track_producer" v-model="track_producer" :class="[app_panel_theme, app_text_theme]">
                                    </div>
                                </div>
                            </div>

                            <!--<div class="form-row">
                                <div class="form-item">
                                    <div class="form-input small active">
                                        <label for="track-tags">Tags</label>
                                        <input type="text" id="track-tags" data-role="tagsinput" name="tags" class="form-control">
                                    </div>
                                </div>
                            </div> -->

                            <!-- DESCRIPTION -->
                            <div class="form-row margin-t-38">
                                <div class="form-item">
                                    <div class="form-input small full" style="height: 270px !important">
                                        <textarea id="profile-description" 
                                            :class="[app_panel_theme, app_text_theme]"
                                            name="profile_description" 
                                            v-model="profile_description" 
                                            placeholder="Write a little description about your track..."
                                            style="overflow: hidden;
                                            padding-bottom: 156px;
                                            padding-top: 14px;
                                            padding-right: 18px;
                                            padding-left: 18px;
                                            resize: none;">
                                        </textarea>
                                        <!-- EMOJI -->
                                        <div class="dropleft emoji-invoker" >
                                            <div data-toggle="dropdown">
                                                <svg height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M0 0h24v24H0z" fill="none"/>
                                                    <template v-if="user_details.app_theme == 'dark'">
                                                        <path fill="#fff" d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                                                    </template>
                                                    <template v-else>
                                                        <path fill="#1d2333" d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                                                    </template>
                                                </svg>
                                            </div>
                                            <ul class="dropdown-menu emoji-dropdown" @click="_dropdownMenuClicked($event)">
                                                <vemojipicker @select="selectEmoji" />
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                  </div>
              </form>
              <!-- /FORM -->
            </div>
            <!-- WIDGET BOX CONTENT -->
          </div>
          <!-- /WIDGET BOX - BASIC DETAILS -->

          <!-- WIDGET BOX - PRIVACY SETTINGS-->
          <div class="widget-box" :class="app_panel_theme">
            <!-- WIDGET BOX TITLE -->
            <p class="widget-box-title" :class="app_text_theme">Privacy Settings</p>
            <!-- /WIDGET BOX TITLE -->

            <!-- WIDGET BOX CONTENT -->
            <div class="widget-box-content">
              <!-- FORM -->
              <form class="form">
                <!-- FORM ROW -->
                <div class="form-row split">
                    <!-- FORM ITEM -->
                    <div class="form-item centered">
                      <label class="form-title" for="profile-privacy-visibility" :class="app_text_theme">Who can see this post?</label>
                    </div>
                    <!-- /FORM ITEM -->

                    <!-- FORM ITEM -->
                    <div class="form-item">
                      <!-- FORM SELECT -->
                      <div class="form-select">
                        <select id="profile-privacy-visibility" name="profile_privacy_visibility" v-model="track_privacy" :class="[app_panel_theme, app_text_theme]">
                            <option value="1">Only Me</option>
                            <option value="2">Members Only</option>
                            <option value="3" selected>Everyone (Public)</option>
                        </select>
                        <!-- FORM SELECT ICON -->
                        <svg class="form-select-icon icon-small-arrow">
                          <use xlink:href="#svg-small-arrow"></use>
                        </svg>
                        <!-- /FORM SELECT ICON -->
                      </div>
                      <!-- /FORM SELECT -->
                    </div>
                    <!-- /FORM ITEM -->
                  </div>
                  <!-- /FORM ROW -->
              </form>
              <!-- /FORM -->
            </div>
            <!-- WIDGET BOX CONTENT -->
          </div>
          <!-- /WIDGET BOX - PRIVACY SETTINGS -->

            <form class="form">
                <div class="form-row">
                    <div class="form-item text-center">
                        <p class="button primary" v-if="uploadProgress != 0 && uploadProgress != 100"
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
                            @click="_userPrompt('Upload Track', 'Are you sure you want to upload this track?', 'question')"
                        >
                            Upload
                        </p>
                    </div>
                </div>
            </form>
            <div class="progress" style="height: 5px; margin-right: 5px; margin-left: 5px; border-radius: 0 !important" v-if="uploadProgress != 0 && uploadProgress != 100">
                <div class="progress-bar-purple progress-bar-striped progress-bar-animated"
                    role="progressbar"
                    :style="{'width': uploadProgress + '%'}"
                    :aria-valuenow="uploadProgress"
                    aria-valuemin="0"
                    aria-valuemax="100">
                </div>
            </div>
        </div>
        <!-- /GRID COLUMN -->
      </div>
      <!-- /GRID COLUMN -->
    </div>
    <!-- /GRID -->
  </div>
  <!-- /CONTENT GRID -->
</div>
</newsfeedupload>
<link href="{{ asset('assets/harmelo/css/newsfeed-upload.css') }}" rel="stylesheet">
@endsection

@section('page_stylesheets')

@endsection
