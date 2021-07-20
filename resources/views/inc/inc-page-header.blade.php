<inc-header inline-template>
    <div>
        <!-- HEADER -->
        <header class="header" :class="app_component_theme">
            <!-- HEADER ACTIONS -->
            <div class="header-actions">
                <!-- HEADER BRAND -->
                <div class="header-brand">
                    <!-- LOGO -->
                    <div class="logo">
                        <!-- ICON LOGO VIKINGER -->
                        <a href="/feed"><img src="{{ asset('assets/harmelo/logo/harmelo-logo-no-word-white.png') }}"
                                height="50"></a>
                        <!-- /ICON LOGO VIKINGER -->
                    </div>
                    <!-- /LOGO -->

                    <!-- HEADER BRAND TEXT -->
                    <a href="/feed">
                        <h1 class="header-brand-text"
                            style="font-family: Pacifico, cursive !important; font-size: 2.25rem; text-transform: none !important">
                            Harmelo</h1><a></a>
                        <!-- /HEADER BRAND TEXT -->
                </div>
                <!-- /HEADER BRAND -->
            </div>
            <!-- /HEADER ACTIONS -->

            <!-- HEADER ACTIONS -->
            <div class="header-actions">
                <!-- SIDEMENU TRIGGER -->
                <!-- <div class="sidemenu-trigger navigation-widget-trigger">
        <svg class="icon-grid">
        <use xlink:href="#svg-grid"></use>
        </svg>
    </div> -->
                <!-- /SIDEMENU TRIGGER -->

                <!-- MOBILEMENU TRIGGER -->
                <div class="mobilemenu-trigger navigation-widget-mobile-trigger">
                    <!-- BURGER ICON -->
                    <div class="burger-icon inverted">
                        <!-- BURGER ICON BAR -->
                        <div class="burger-icon-bar"></div>
                        <!-- /BURGER ICON BAR -->

                        <!-- BURGER ICON BAR -->
                        <div class="burger-icon-bar"></div>
                        <!-- /BURGER ICON BAR -->

                        <!-- BURGER ICON BAR -->
                        <div class="burger-icon-bar"></div>
                        <!-- /BURGER ICON BAR -->
                    </div>
                    <!-- /BURGER ICON -->
                </div>
                <!-- /MOBILEMENU TRIGGER -->

                <!-- NAVIGATION -->
                <nav class="navigation">
                    <!-- MENU MAIN -->
                    <ul class="menu-main">
                        <!-- MENU MAIN ITEM -->
                        <li class="menu-main-item">
                            <!-- MENU MAIN ITEM LINK -->
                            <a class="menu-main-item-link" href="/feed">Music Feed</a>
                            <!-- /MENU MAIN ITEM LINK -->
                        </li>
                        <!-- /MENU MAIN ITEM -->

                        <!-- MENU MAIN ITEM -->
                        <li class="menu-main-item">
                            <!-- MENU MAIN ITEM LINK -->
                            <a class="menu-main-item-link" href="#">Blogs</a>
                            <!-- /MENU MAIN ITEM LINK -->
                        </li>
                        <!-- /MENU MAIN ITEM -->

                        <!-- MENU MAIN ITEM -->
                        <li class="menu-main-item">
                            <!-- MENU MAIN ITEM LINK -->
                            <a class="menu-main-item-link" href="/under-construction">Marketplace</a>
                            <!-- /MENU MAIN ITEM LINK -->
                        </li>
                        <!-- /MENU MAIN ITEM -->
                    </ul>
                    <!-- /MENU MAIN -->
                </nav>
                <!-- /NAVIGATION -->
            </div>
            <!-- /HEADER ACTIONS -->

            <!-- HEADER ACTIONS -->
            <div class="header-actions search-bar">
                <!-- INTERACTIVE INPUT -->
                <div class="interactive-input dark">
                    <input type="text" id="search-main" name="search_main" class="header-dropdown-trigger"
                        placeholder="Search here for people or groups"
                        v-model="user_search_query"
                    >
                    <div class="dropdown-box header-dropdown" style="width: 562px !important; top: 51px">
                        <!-- DROPDOWN BOX HEADER -->
                        <div class="dropdown-box-header">
                            <!-- DROPDOWN BOX HEADER TITLE -->
                            <p class="dropdown-box-header-title">Result</p>
                            <!-- /DROPDOWN BOX HEADER TITLE -->
                        </div>
                        <!-- /DROPDOWN BOX HEADER -->

                        <!-- DROPDOWN BOX LIST -->
                        <div class="dropdown-box-list no-hover" data-simplebar v-if="user_search_result.length > 0">
                            <div class="dropdown-box-list-item" v-for="(search, ss) in user_search_result">
                                <div class="user-status request" @click="_redirectToUser(search.slug)" style="cursor: pointer">
                                    <a class="user-status-avatar" :href="'/user/'+search.slug">
                                        <div class="user-avatar small no-outline">
                                            <div class="user-avatar-content"
                                                :style="[search.avatar != null
                                                    ? {'background':'transparent'}
                                                    : {'background':'linear-gradient(150deg, rgb(76 37 204 / 69%), rgb(255 40 149 / 60%))'}]"
                                                style="
                                                border-radius: 50%;
                                                padding: 2px;
                                                left: 3px !important;
                                                width: 100% !important;
                                                text-align: center">
                                                <img v-if="search.avatar != null"
                                                    :src="!search.avatar.includes('https') ?
                                                        s3_site + 'public/user/' + search.id + '/avatar/' + search.avatar
                                                        : search.avatar" height="34" style="border-radius: 50% !important" width="34">
                                                <img v-else src="{{ asset('assets/harmelo/image/default_avatar.png') }}" height="34" style="border-radius: 50% !important" width="34">
                                            </div>
                                        </div>
                                    </a>

                                    <!-- USER STATUS TITLE -->
                                    <p class="user-status-title"><a class="bold">@{{ search.name }}</a></p>
                                    <!-- /USER STATUS TITLE -->

                                    <!-- USER STATUS TEXT -->
                                    <p class="user-status-text">@@{{ search.slug }}</p>
                                    <!-- /USER STATUS TEXT -->

                                    <!-- ACTION REQUEST LIST -->
                                    <div class="action-request-list">
                                        <!-- ACTION REQUEST -->
                                        <div class="action-request accept">
                                            <!-- ACTION REQUEST ICON -->
                                            <svg class="action-request-icon icon-add-friend">
                                                <use xlink:href="#svg-add-friend"></use>
                                            </svg>
                                            <!-- /ACTION REQUEST ICON -->
                                        </div>
                                        <!-- /ACTION REQUEST -->

                                        <!-- ACTION REQUEST -->
                                        <div class="action-request decline">
                                            <!-- ACTION REQUEST ICON -->
                                            <svg class="action-request-icon icon-remove-friend">
                                                <use xlink:href="#svg-remove-friend"></use>
                                            </svg>
                                            <!-- /ACTION REQUEST ICON -->
                                        </div>
                                        <!-- /ACTION REQUEST -->
                                    </div>
                                    <!-- ACTION REQUEST LIST -->
                                </div>
                                <!-- /USER STATUS -->
                            </div>
                        </div>
                        <!-- /DROPDOWN BOX LIST -->

                        <!-- DROPDOWN BOX BUTTON -->
                        <a class="dropdown-box-button secondary" href="hub-profile-requests.html">
                            <template v-if="is_search_querying">Searching ...</template>
                            <template v-if="user_search_query !== '' && !is_search_querying">@{{ user_search_result.length }} matches for @{{ user_search_query }}</template>
                            <template v-if="user_search_query === ''">Search user</template>
                        </a>
                        <!-- /DROPDOWN BOX BUTTON -->
                    </div>
                    <!-- INTERACTIVE INPUT ICON WRAP -->
                    <div class="interactive-input-icon-wrap">
                        <!-- INTERACTIVE INPUT ICON -->
                        <svg class="interactive-input-icon icon-magnifying-glass">
                            <use xlink:href="#svg-magnifying-glass"></use>
                        </svg>
                        <!-- /INTERACTIVE INPUT ICON -->
                    </div>
                    <!-- /INTERACTIVE INPUT ICON WRAP -->

                    <!-- INTERACTIVE INPUT ACTION -->
                    <div class="interactive-input-action">
                        <!-- INTERACTIVE INPUT ACTION ICON -->
                        <svg class="interactive-input-action-icon icon-cross-thin">
                            <use xlink:href="#svg-cross-thin"></use>
                        </svg>
                        <!-- /INTERACTIVE INPUT ACTION ICON -->
                    </div>
                    <!-- /INTERACTIVE INPUT ACTION -->
                </div>
                <!-- /INTERACTIVE INPUT -->
            </div>
            <!-- /HEADER ACTIONS -->

            <!-- HEADER ACTIONS -->
            <div class="header-actions">
                <div class="muzieknootjes heart animated css">
                    <a style="text-align: cente; font-weight: 900; position: absolute; top: 35%; left: 23%;"
                        :href="'http://localhost:8081/access_code/' + user_details.access_code" style="color: #fff">Enroll Now !
                    </a>

                    <div class="noot-1">
                        &#9835; &#9833;
                    </div>
                    <div class="noot-2">
                        &#9833;
                    </div>
                    <div class="noot-3">
                        &#9839; &#9834;
                    </div>
                    <div class="noot-4">
                        &#9834;
                    </div>
                </div>

                <!-- PROGRESS STAT -->
                <div class="progress-stat" style="visibility: hidden">
                    <!-- BAR PROGRESS WRAP -->
                    <div class="bar-progress-wrap">
                        <!-- BAR PROGRESS INFO -->
                        <p class="bar-progress-info">Next: <span class="bar-progress-text"></span></p>
                        <!-- /BAR PROGRESS INFO -->
                    </div>
                    <!-- /BAR PROGRESS WRAP -->

                    <!-- PROGRESS STAT BAR -->
                    <div id="logged-user-level" class="progress-stat-bar"></div>
                    <!-- /PROGRESS STAT BAR -->
                </div>
                <!-- /PROGRESS STAT -->
            </div>
            <!-- /HEADER ACTIONS -->

            <!-- HEADER ACTIONS -->
            <div class="header-actions">
                <!-- ACTION LIST -->
                <div class="action-list dark">

                    <div class="action-list-item-wrap">
                        <div class="action-list-item" @click="changeapptheme(user_details.app_theme == 'light' ? 'dark' : 'light')">
                            <i class="material-icons action-list-item-icon text-white" v-if="user_details.app_theme == 'light'">nights_stay</i>
                            <i class="material-icons action-list-item-icon text-white" v-else>wb_sunny</i>
                        </div>
                    </div>

                    <!-- ACTION LIST ITEM WRAP -->
                    <div class="action-list-item-wrap">
                        <!-- ACTION LIST ITEM -->
                        <div class="action-list-item header-dropdown-trigger">
                            <!-- ACTION LIST ITEM ICON -->
                            <svg class="action-list-item-icon icon-friend">
                                <use xlink:href="#svg-friend"></use>
                            </svg>
                            <!-- /ACTION LIST ITEM ICON -->
                        </div>
                        <!-- /ACTION LIST ITEM -->

                        <!-- DROPDOWN BOX -->
                        <div class="dropdown-box header-dropdown">
                            <!-- DROPDOWN BOX HEADER -->
                            <div class="dropdown-box-header">
                                <!-- DROPDOWN BOX HEADER TITLE -->
                                <p class="dropdown-box-header-title">Friend Requests</p>
                                <!-- /DROPDOWN BOX HEADER TITLE -->

                                <!-- DROPDOWN BOX HEADER ACTIONS -->
                                <div class="dropdown-box-header-actions">
                                    <!-- DROPDOWN BOX HEADER ACTION -->
                                    <p class="dropdown-box-header-action">Find Friends</p>
                                    <!-- /DROPDOWN BOX HEADER ACTION -->

                                    <!-- DROPDOWN BOX HEADER ACTION -->
                                    <p class="dropdown-box-header-action">Settings</p>
                                    <!-- /DROPDOWN BOX HEADER ACTION -->
                                </div>
                                <!-- /DROPDOWN BOX HEADER ACTIONS -->
                            </div>
                            <!-- /DROPDOWN BOX HEADER -->

                            <!-- DROPDOWN BOX LIST -->
                            <div class="dropdown-box-list no-hover" data-simplebar v-if="friend_request_list.length > 0">
                                <div class="dropdown-box-list-item" v-for="(request, rr) in friend_request_list">
                                    <!-- USER STATUS -->
                                    <div class="user-status request">
                                        <!-- USER STATUS AVATAR -->
                                        <a class="user-status-avatar" :href="'/user/' + request.user_details[0].slug">
                                            <!-- USER AVATAR -->
                                            <div class="user-avatar small no-outline">
                                                <!-- USER AVATAR CONTENT -->
                                                <div class="user-avatar-content">
                                                    <img :src="s3_site+'public/user/'+request.user_details[0].id+'/avatar/'+request.user_details[0].avatar" v-if="request.user_details[0].avatar != null" width="32"
                                                        style="border-radius: 50%; width: 32px; height: 32px">
                                                    <img v-else src="{{ asset('assets/harmelo/image/default_avatar.png') }}" width="32"
                                                        style="border-radius: 50%; width: 32px; height: 32px">
                                                </div>
                                                <!-- /USER AVATAR CONTENT -->
                                            </div>
                                            <!-- /USER AVATAR -->
                                        </a>
                                        <!-- /USER STATUS AVATAR -->

                                        <!-- USER STATUS TITLE -->
                                        <p class="user-status-title"><a class="bold"
                                            :href="'/user/' + request.user_details[0].slug">@{{ request.user_details[0] . name }}</a></p>
                                        <!-- /USER STATUS TITLE -->

                                        <!-- USER STATUS TEXT -->
                                        <p class="user-status-text">6 friends in common</p>
                                        <!-- /USER STATUS TEXT -->

                                        <!-- ACTION REQUEST LIST -->
                                        <div class="action-request-list">
                                            <!-- ACTION REQUEST -->
                                            <div class="action-request accept">
                                                <!-- ACTION REQUEST ICON -->
                                                <svg class="action-request-icon icon-add-friend">
                                                    <use xlink:href="#svg-add-friend"></use>
                                                </svg>
                                                <!-- /ACTION REQUEST ICON -->
                                            </div>
                                            <!-- /ACTION REQUEST -->

                                            <!-- ACTION REQUEST -->
                                            <div class="action-request decline">
                                                <!-- ACTION REQUEST ICON -->
                                                <svg class="action-request-icon icon-remove-friend">
                                                    <use xlink:href="#svg-remove-friend"></use>
                                                </svg>
                                                <!-- /ACTION REQUEST ICON -->
                                            </div>
                                            <!-- /ACTION REQUEST -->
                                        </div>
                                        <!-- ACTION REQUEST LIST -->
                                    </div>
                                    <!-- /USER STATUS -->
                                </div>
                            </div>
                            <!-- /DROPDOWN BOX LIST -->

                            <!-- DROPDOWN BOX BUTTON -->
                            <a class="dropdown-box-button secondary" href="hub-profile-requests.html">
                                <template v-if="friend_request_list.length > 0">
                                    View all requests
                                </template>
                                <template v-else>
                                    No friend request
                                </template>
                            </a>
                            <!-- /DROPDOWN BOX BUTTON -->
                        </div>
                        <!-- /DROPDOWN BOX -->
                    </div>
                    <!-- /ACTION LIST ITEM WRAP -->

                    <!-- ACTION LIST ITEM WRAP -->
                    <div class="action-list-item-wrap">
                        <!-- ACTION LIST ITEM -->
                        <div class="action-list-item unread header-dropdown-trigger">
                            <!-- ACTION LIST ITEM ICON -->
                            <svg class="action-list-item-icon icon-notification">
                                <use xlink:href="#svg-notification"></use>
                            </svg>
                            <!-- /ACTION LIST ITEM ICON -->
                        </div>
                        <!-- /ACTION LIST ITEM -->

                        <!-- DROPDOWN BOX -->
                        <div class="dropdown-box header-dropdown">
                            <!-- DROPDOWN BOX HEADER -->
                            <div class="dropdown-box-header">
                                <!-- DROPDOWN BOX HEADER TITLE -->
                                <p class="dropdown-box-header-title">Notifications</p>
                                <!-- /DROPDOWN BOX HEADER TITLE -->

                                <!-- DROPDOWN BOX HEADER ACTIONS -->
                                <div class="dropdown-box-header-actions">
                                    <!-- DROPDOWN BOX HEADER ACTION -->
                                    <p class="dropdown-box-header-action">Mark all as Read</p>
                                    <!-- /DROPDOWN BOX HEADER ACTION -->

                                    <!-- DROPDOWN BOX HEADER ACTION -->
                                    <p class="dropdown-box-header-action">Settings</p>
                                    <!-- /DROPDOWN BOX HEADER ACTION -->
                                </div>
                                <!-- /DROPDOWN BOX HEADER ACTIONS -->
                            </div>
                            <!-- /DROPDOWN BOX HEADER -->

                            <!-- DROPDOWN BOX LIST -->
                            <div class="dropdown-box-list" data-simplebar>
                                <!-- DROPDOWN BOX LIST ITEM -->
                                <div class="dropdown-box-list-item unread">
                                    <!-- USER STATUS -->
                                    <div class="user-status notification">
                                        <!-- USER STATUS AVATAR -->
                                        <a class="user-status-avatar" href="profile-timeline.html">
                                            <!-- USER AVATAR -->
                                            <div class="user-avatar small no-outline">
                                                <!-- USER AVATAR CONTENT -->
                                                <div class="user-avatar-content">
                                                    <!-- HEXAGON -->
                                                    <div class="hexagon-image-30-32"
                                                        data-src="{{ asset('assets/vikinger/img/avatar/03.jpg') }}">
                                                    </div>
                                                    <!-- /HEXAGON -->
                                                </div>
                                                <!-- /USER AVATAR CONTENT -->

                                                <!-- USER AVATAR PROGRESS -->
                                                <div class="user-avatar-progress">
                                                    <!-- HEXAGON -->
                                                    <div class="hexagon-progress-40-44"></div>
                                                    <!-- /HEXAGON -->
                                                </div>
                                                <!-- /USER AVATAR PROGRESS -->

                                                <!-- USER AVATAR PROGRESS BORDER -->
                                                <div class="user-avatar-progress-border">
                                                    <!-- HEXAGON -->
                                                    <div class="hexagon-border-40-44"></div>
                                                    <!-- /HEXAGON -->
                                                </div>
                                                <!-- /USER AVATAR PROGRESS BORDER -->

                                                <!-- USER AVATAR BADGE -->
                                                <div class="user-avatar-badge">
                                                    <!-- USER AVATAR BADGE BORDER -->
                                                    <div class="user-avatar-badge-border">
                                                        <!-- HEXAGON -->
                                                        <div class="hexagon-22-24"></div>
                                                        <!-- /HEXAGON -->
                                                    </div>
                                                    <!-- /USER AVATAR BADGE BORDER -->

                                                    <!-- USER AVATAR BADGE CONTENT -->
                                                    <div class="user-avatar-badge-content">
                                                        <!-- HEXAGON -->
                                                        <div class="hexagon-dark-16-18"></div>
                                                        <!-- /HEXAGON -->
                                                    </div>
                                                    <!-- /USER AVATAR BADGE CONTENT -->

                                                    <!-- USER AVATAR BADGE TEXT -->
                                                    <p class="user-avatar-badge-text">16</p>
                                                    <!-- /USER AVATAR BADGE TEXT -->
                                                </div>
                                                <!-- /USER AVATAR BADGE -->
                                            </div>
                                            <!-- /USER AVATAR -->
                                        </a>
                                        <!-- /USER STATUS AVATAR -->

                                        <!-- USER STATUS TITLE -->
                                        <p class="user-status-title"><a class="bold" href="profile-timeline.html">Nick
                                                Grissom</a> posted a comment on your <a class="highlighted"
                                                href="profile-timeline.html">status update</a></p>
                                        <!-- /USER STATUS TITLE -->

                                        <!-- USER STATUS TIMESTAMP -->
                                        <p class="user-status-timestamp">2 minutes ago</p>
                                        <!-- /USER STATUS TIMESTAMP -->

                                        <!-- USER STATUS ICON -->
                                        <div class="user-status-icon">
                                            <!-- ICON COMMENT -->
                                            <svg class="icon-comment">
                                                <use xlink:href="#svg-comment"></use>
                                            </svg>
                                            <!-- /ICON COMMENT -->
                                        </div>
                                        <!-- /USER STATUS ICON -->
                                    </div>
                                    <!-- /USER STATUS -->
                                </div>
                                <!-- /DROPDOWN BOX LIST ITEM -->

                                <!-- DROPDOWN BOX LIST ITEM -->
                                <div class="dropdown-box-list-item">
                                    <!-- USER STATUS -->
                                    <div class="user-status notification">
                                        <!-- USER STATUS AVATAR -->
                                        <a class="user-status-avatar" href="profile-timeline.html">
                                            <!-- USER AVATAR -->
                                            <div class="user-avatar small no-outline">
                                                <!-- USER AVATAR CONTENT -->
                                                <div class="user-avatar-content">
                                                    <!-- HEXAGON -->
                                                    <div class="hexagon-image-30-32"
                                                        data-src="{{ asset('assets/vikinger/img/avatar/07.jpg') }}">
                                                    </div>
                                                    <!-- /HEXAGON -->
                                                </div>
                                                <!-- /USER AVATAR CONTENT -->

                                                <!-- USER AVATAR PROGRESS -->
                                                <div class="user-avatar-progress">
                                                    <!-- HEXAGON -->
                                                    <div class="hexagon-progress-40-44"></div>
                                                    <!-- /HEXAGON -->
                                                </div>
                                                <!-- /USER AVATAR PROGRESS -->

                                                <!-- USER AVATAR PROGRESS BORDER -->
                                                <div class="user-avatar-progress-border">
                                                    <!-- HEXAGON -->
                                                    <div class="hexagon-border-40-44"></div>
                                                    <!-- /HEXAGON -->
                                                </div>
                                                <!-- /USER AVATAR PROGRESS BORDER -->

                                                <!-- USER AVATAR BADGE -->
                                                <div class="user-avatar-badge">
                                                    <!-- USER AVATAR BADGE BORDER -->
                                                    <div class="user-avatar-badge-border">
                                                        <!-- HEXAGON -->
                                                        <div class="hexagon-22-24"></div>
                                                        <!-- /HEXAGON -->
                                                    </div>
                                                    <!-- /USER AVATAR BADGE BORDER -->

                                                    <!-- USER AVATAR BADGE CONTENT -->
                                                    <div class="user-avatar-badge-content">
                                                        <!-- HEXAGON -->
                                                        <div class="hexagon-dark-16-18"></div>
                                                        <!-- /HEXAGON -->
                                                    </div>
                                                    <!-- /USER AVATAR BADGE CONTENT -->

                                                    <!-- USER AVATAR BADGE TEXT -->
                                                    <p class="user-avatar-badge-text">26</p>
                                                    <!-- /USER AVATAR BADGE TEXT -->
                                                </div>
                                                <!-- /USER AVATAR BADGE -->
                                            </div>
                                            <!-- /USER AVATAR -->
                                        </a>
                                        <!-- /USER STATUS AVATAR -->

                                        <!-- USER STATUS TITLE -->
                                        <p class="user-status-title"><a class="bold" href="profile-timeline.html">Sarah
                                                Diamond</a> left a like <img class="reaction"
                                                src="{{ asset('assets/vikinger/img/reaction/like.png') }} "
                                                alt="reaction-like"> reaction on your <a class="highlighted"
                                                href="profile-timeline.html">status update</a></p>
                                        <!-- /USER STATUS TITLE -->

                                        <!-- USER STATUS TIMESTAMP -->
                                        <p class="user-status-timestamp">17 minutes ago</p>
                                        <!-- /USER STATUS TIMESTAMP -->

                                        <!-- USER STATUS ICON -->
                                        <div class="user-status-icon">
                                            <!-- ICON THUMBS UP -->
                                            <svg class="icon-thumbs-up">
                                                <use xlink:href="#svg-thumbs-up"></use>
                                            </svg>
                                            <!-- /ICON THUMBS UP -->
                                        </div>
                                        <!-- /USER STATUS ICON -->
                                    </div>
                                    <!-- /USER STATUS -->
                                </div>
                                <!-- /DROPDOWN BOX LIST ITEM -->

                                <!-- DROPDOWN BOX LIST ITEM -->
                                <div class="dropdown-box-list-item">
                                    <!-- USER STATUS -->
                                    <div class="user-status notification">
                                        <!-- USER STATUS AVATAR -->
                                        <a class="user-status-avatar" href="profile-timeline.html">
                                            <!-- USER AVATAR -->
                                            <div class="user-avatar small no-outline">
                                                <!-- USER AVATAR CONTENT -->
                                                <div class="user-avatar-content">
                                                    <!-- HEXAGON -->
                                                    <div class="hexagon-image-30-32"
                                                        data-src="{{ asset('assets/vikinger/img/avatar/02.jpg') }}">
                                                    </div>
                                                    <!-- /HEXAGON -->
                                                </div>
                                                <!-- /USER AVATAR CONTENT -->

                                                <!-- USER AVATAR PROGRESS -->
                                                <div class="user-avatar-progress">
                                                    <!-- HEXAGON -->
                                                    <div class="hexagon-progress-40-44"></div>
                                                    <!-- /HEXAGON -->
                                                </div>
                                                <!-- /USER AVATAR PROGRESS -->

                                                <!-- USER AVATAR PROGRESS BORDER -->
                                                <div class="user-avatar-progress-border">
                                                    <!-- HEXAGON -->
                                                    <div class="hexagon-border-40-44"></div>
                                                    <!-- /HEXAGON -->
                                                </div>
                                                <!-- /USER AVATAR PROGRESS BORDER -->

                                                <!-- USER AVATAR BADGE -->
                                                <div class="user-avatar-badge">
                                                    <!-- USER AVATAR BADGE BORDER -->
                                                    <div class="user-avatar-badge-border">
                                                        <!-- HEXAGON -->
                                                        <div class="hexagon-22-24"></div>
                                                        <!-- /HEXAGON -->
                                                    </div>
                                                    <!-- /USER AVATAR BADGE BORDER -->

                                                    <!-- USER AVATAR BADGE CONTENT -->
                                                    <div class="user-avatar-badge-content">
                                                        <!-- HEXAGON -->
                                                        <div class="hexagon-dark-16-18"></div>
                                                        <!-- /HEXAGON -->
                                                    </div>
                                                    <!-- /USER AVATAR BADGE CONTENT -->

                                                    <!-- USER AVATAR BADGE TEXT -->
                                                    <p class="user-avatar-badge-text">13</p>
                                                    <!-- /USER AVATAR BADGE TEXT -->
                                                </div>
                                                <!-- /USER AVATAR BADGE -->
                                            </div>
                                            <!-- /USER AVATAR -->
                                        </a>
                                        <!-- /USER STATUS AVATAR -->

                                        <!-- USER STATUS TITLE -->
                                        <p class="user-status-title"><a class="bold"
                                                href="profile-timeline.html">Destroy Dex</a> posted a comment on your <a
                                                class="highlighted" href="profile-photos.html">photo</a></p>
                                        <!-- /USER STATUS TITLE -->

                                        <!-- USER STATUS TIMESTAMP -->
                                        <p class="user-status-timestamp">31 minutes ago</p>
                                        <!-- /USER STATUS TIMESTAMP -->

                                        <!-- USER STATUS ICON -->
                                        <div class="user-status-icon">
                                            <!-- ICON COMMENT -->
                                            <svg class="icon-comment">
                                                <use xlink:href="#svg-comment"></use>
                                            </svg>
                                            <!-- /ICON COMMENT -->
                                        </div>
                                        <!-- /USER STATUS ICON -->
                                    </div>
                                    <!-- /USER STATUS -->
                                </div>
                                <!-- /DROPDOWN BOX LIST ITEM -->

                                <!-- DROPDOWN BOX LIST ITEM -->
                                <div class="dropdown-box-list-item">
                                    <!-- USER STATUS -->
                                    <div class="user-status notification">
                                        <!-- USER STATUS AVATAR -->
                                        <a class="user-status-avatar" href="profile-timeline.html">
                                            <!-- USER AVATAR -->
                                            <div class="user-avatar small no-outline">
                                                <!-- USER AVATAR CONTENT -->
                                                <div class="user-avatar-content">
                                                    <!-- HEXAGON -->
                                                    <div class="hexagon-image-30-32"
                                                        data-src="{{ asset('assets/vikinger/img/avatar/10.jpg') }}">
                                                    </div>
                                                    <!-- /HEXAGON -->
                                                </div>
                                                <!-- /USER AVATAR CONTENT -->

                                                <!-- USER AVATAR PROGRESS -->
                                                <div class="user-avatar-progress">
                                                    <!-- HEXAGON -->
                                                    <div class="hexagon-progress-40-44"></div>
                                                    <!-- /HEXAGON -->
                                                </div>
                                                <!-- /USER AVATAR PROGRESS -->

                                                <!-- USER AVATAR PROGRESS BORDER -->
                                                <div class="user-avatar-progress-border">
                                                    <!-- HEXAGON -->
                                                    <div class="hexagon-border-40-44"></div>
                                                    <!-- /HEXAGON -->
                                                </div>
                                                <!-- /USER AVATAR PROGRESS BORDER -->

                                                <!-- USER AVATAR BADGE -->
                                                <div class="user-avatar-badge">
                                                    <!-- USER AVATAR BADGE BORDER -->
                                                    <div class="user-avatar-badge-border">
                                                        <!-- HEXAGON -->
                                                        <div class="hexagon-22-24"></div>
                                                        <!-- /HEXAGON -->
                                                    </div>
                                                    <!-- /USER AVATAR BADGE BORDER -->

                                                    <!-- USER AVATAR BADGE CONTENT -->
                                                    <div class="user-avatar-badge-content">
                                                        <!-- HEXAGON -->
                                                        <div class="hexagon-dark-16-18"></div>
                                                        <!-- /HEXAGON -->
                                                    </div>
                                                    <!-- /USER AVATAR BADGE CONTENT -->

                                                    <!-- USER AVATAR BADGE TEXT -->
                                                    <p class="user-avatar-badge-text">5</p>
                                                    <!-- /USER AVATAR BADGE TEXT -->
                                                </div>
                                                <!-- /USER AVATAR BADGE -->
                                            </div>
                                            <!-- /USER AVATAR -->
                                        </a>
                                        <!-- /USER STATUS AVATAR -->

                                        <!-- USER STATUS TITLE -->
                                        <p class="user-status-title"><a class="bold" href="profile-timeline.html">The
                                                Green Goo</a> left a love <img class="reaction"
                                                src="{{ asset('assets/vikinger/img/reaction/love.png') }} "
                                                alt="reaction-love"> reaction on your <a class="highlighted"
                                                href="profile-timeline.html">status update</a></p>
                                        <!-- /USER STATUS TITLE -->

                                        <!-- USER STATUS TIMESTAMP -->
                                        <p class="user-status-timestamp">2 hours ago</p>
                                        <!-- /USER STATUS TIMESTAMP -->

                                        <!-- USER STATUS ICON -->
                                        <div class="user-status-icon">
                                            <!-- ICON THUMBS UP -->
                                            <svg class="icon-thumbs-up">
                                                <use xlink:href="#svg-thumbs-up"></use>
                                            </svg>
                                            <!-- /ICON THUMBS UP -->
                                        </div>
                                        <!-- /USER STATUS ICON -->
                                    </div>
                                    <!-- /USER STATUS -->
                                </div>
                                <!-- /DROPDOWN BOX LIST ITEM -->

                                <!-- DROPDOWN BOX LIST ITEM -->
                                <div class="dropdown-box-list-item">
                                    <!-- USER STATUS -->
                                    <div class="user-status notification">
                                        <!-- USER STATUS AVATAR -->
                                        <a class="user-status-avatar" href="profile-timeline.html">
                                            <!-- USER AVATAR -->
                                            <div class="user-avatar small no-outline">
                                                <!-- USER AVATAR CONTENT -->
                                                <div class="user-avatar-content">
                                                    <!-- HEXAGON -->
                                                    <div class="hexagon-image-30-32"
                                                        data-src="{{ asset('assets/vikinger/img/avatar/05.jpg') }}">
                                                    </div>
                                                    <!-- /HEXAGON -->
                                                </div>
                                                <!-- /USER AVATAR CONTENT -->

                                                <!-- USER AVATAR PROGRESS -->
                                                <div class="user-avatar-progress">
                                                    <!-- HEXAGON -->
                                                    <div class="hexagon-progress-40-44"></div>
                                                    <!-- /HEXAGON -->
                                                </div>
                                                <!-- /USER AVATAR PROGRESS -->

                                                <!-- USER AVATAR PROGRESS BORDER -->
                                                <div class="user-avatar-progress-border">
                                                    <!-- HEXAGON -->
                                                    <div class="hexagon-border-40-44"></div>
                                                    <!-- /HEXAGON -->
                                                </div>
                                                <!-- /USER AVATAR PROGRESS BORDER -->

                                                <!-- USER AVATAR BADGE -->
                                                <div class="user-avatar-badge">
                                                    <!-- USER AVATAR BADGE BORDER -->
                                                    <div class="user-avatar-badge-border">
                                                        <!-- HEXAGON -->
                                                        <div class="hexagon-22-24"></div>
                                                        <!-- /HEXAGON -->
                                                    </div>
                                                    <!-- /USER AVATAR BADGE BORDER -->

                                                    <!-- USER AVATAR BADGE CONTENT -->
                                                    <div class="user-avatar-badge-content">
                                                        <!-- HEXAGON -->
                                                        <div class="hexagon-dark-16-18"></div>
                                                        <!-- /HEXAGON -->
                                                    </div>
                                                    <!-- /USER AVATAR BADGE CONTENT -->

                                                    <!-- USER AVATAR BADGE TEXT -->
                                                    <p class="user-avatar-badge-text">12</p>
                                                    <!-- /USER AVATAR BADGE TEXT -->
                                                </div>
                                                <!-- /USER AVATAR BADGE -->
                                            </div>
                                            <!-- /USER AVATAR -->
                                        </a>
                                        <!-- /USER STATUS AVATAR -->

                                        <!-- USER STATUS TITLE -->
                                        <p class="user-status-title"><a class="bold" href="profile-timeline.html">Neko
                                                Bebop</a> posted a comment on your <a class="highlighted"
                                                href="profile-timeline.html">status update</a></p>
                                        <!-- /USER STATUS TITLE -->

                                        <!-- USER STATUS TIMESTAMP -->
                                        <p class="user-status-timestamp">3 hours ago</p>
                                        <!-- /USER STATUS TIMESTAMP -->

                                        <!-- USER STATUS ICON -->
                                        <div class="user-status-icon">
                                            <!-- ICON COMMENT -->
                                            <svg class="icon-comment">
                                                <use xlink:href="#svg-comment"></use>
                                            </svg>
                                            <!-- /ICON COMMENT -->
                                        </div>
                                        <!-- /USER STATUS ICON -->
                                    </div>
                                    <!-- /USER STATUS -->
                                </div>
                                <!-- /DROPDOWN BOX LIST ITEM -->
                            </div>
                            <!-- /DROPDOWN BOX LIST -->

                            <!-- DROPDOWN BOX BUTTON -->
                            <a class="dropdown-box-button secondary" href="hub-profile-notifications.html">View all
                                Notifications</a>
                            <!-- /DROPDOWN BOX BUTTON -->
                        </div>
                        <!-- /DROPDOWN BOX -->
                    </div>
                    <!-- /ACTION LIST ITEM WRAP -->
                </div>
                <!-- /ACTION LIST -->

                <!-- ACTION ITEM WRAP -->
                <div class="action-item-wrap">
                    <!-- ACTION ITEM -->
                    <div class="action-item dark header-settings-dropdown-trigger">
                        <!-- ACTION ITEM ICON -->
                        <svg class="action-item-icon icon-settings">
                            <use xlink:href="#svg-settings"></use>
                        </svg>
                        <!-- /ACTION ITEM ICON -->
                    </div>
                    <!-- /ACTION ITEM -->

                    <!-- DROPDOWN NAVIGATION -->
                    <div class="dropdown-navigation header-settings-dropdown">
                        <!-- DROPDOWN NAVIGATION HEADER -->
                        <div class="dropdown-navigation-header">
                            <!-- USER STATUS -->
                            <div class="user-status">
                                <!-- USER STATUS AVATAR -->
                                <a class="user-status-avatar" :href="'/user/' + user_details.slug">
                                    <!-- USER AVATAR -->
                                    <div class="user-avatar small no-outline">
                                        <div class="user-avatar-content"
                                        :style="[user_details.avatar != null ? {'background':'transparent'} : {'background':'linear-gradient(150deg, rgb(76 37 204 / 69%), rgb(255 40 149 / 60%))'}]"
                                            style="
                                            border-radius: 50%;
                                            padding: 2px;
                                            left: 3px !important;
                                            width: 100% !important;
                                            text-align: center">
                                            <img v-if="user_details.avatar != 'null' && user_details.avatar != null"
                                                :src="!user_details.avatar.includes('https') ?
                                                    s3_site + 'public/user/' + user_details.id + '/avatar/' + user_details.avatar
                                                    : user_details.avatar" height="34" style="border-radius: 50% !important" width="34">
                                            <img v-else src="{{ asset('assets/harmelo/image/default_avatar.png') }}" height="34" style="border-radius: 50% !important" width="34">
                                        </div>
                                    </div>
                                    <!-- /USER AVATAR -->
                                </a>
                                <p class="user-status-title"><a :href="'/user/' + user_details.slug"
                                        style="color: #fff !important">Hi, @{{ user_details . name }}</a></p>
                                <p class="user-status-text small"><a :href="'/user/' + user_details.slug">@@{{ user_details . slug }}</a>
                                </p>
                            </div>
                            <!-- /USER STATUS -->
                        </div>
                        <!-- /DROPDOWN NAVIGATION HEADER -->

                        <p class="dropdown-navigation-category">Account</p>
                        <a class="dropdown-navigation-link" :href="'/user/' + user_details.slug">Profile Info</a>
                        <a class="dropdown-navigation-link" :href="'/user/settings/' + user_details.slug">Edit Profile</a>

                        <p class="dropdown-navigation-category">Settings</p>
                        <a class="dropdown-navigation-link" href="hub-account-info.html">Account Info</a>
                        <a class="dropdown-navigation-link" href="hub-account-password.html">Change Password</a>
                        <a class="dropdown-navigation-link" href="hub-account-settings.html">General Settings</a>

                        <!-- DROPDOWN NAVIGATION BUTTON -->
                        <a class="dropdown-navigation-button button small secondary" href="/logout"
                            @click="_logOut()">Logout</a>
                        <!-- /DROPDOWN NAVIGATION BUTTON -->
                    </div>
                    <!-- /DROPDOWN NAVIGATION -->
                </div>
                <!-- /ACTION ITEM WRAP -->
            </div>
            <!-- /HEADER ACTIONS -->
        </header>
        <!-- /HEADER -->

    </div>
</inc-header>

<link href="{{ asset('assets/harmelo/css/header.css') }}" rel="stylesheet">
