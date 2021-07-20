<inc-floatybar inline-template>
<!-- FLOATY BAR -->
<aside class="floaty-bar">
  <!-- BAR ACTIONS -->
  <div class="bar-actions">
    <!-- NAVIGATION -->
    <nav class="navigation">
      <!-- MENU MAIN -->
      <ul class="menu-main">
        <!-- MENU MAIN ITEM -->
        <li class="menu-main-item">
          <!-- MENU MAIN ITEM LINK -->
          <a class="menu-main-item-link" href="#">Music Feed</a>
          <!-- /MENU MAIN ITEM LINK -->
        </li>
        <!-- /MENU MAIN ITEM -->

         <!-- MENU MAIN ITEM -->
         <li class="menu-main-item">
          <!-- MENU MAIN ITEM LINK -->
          <a class="menu-main-item-link" href="#">Education</a>
          <!-- /MENU MAIN ITEM LINK -->
        </li>
        <!-- /MENU MAIN ITEM -->

        <!-- MENU MAIN ITEM -->
        <li class="menu-main-item">
          <!-- MENU MAIN ITEM LINK -->
          <p class="menu-main-item-link">
            <!-- ICON DOTS -->
            <svg class="icon-dots">
              <use xlink:href="#svg-dots"></use>
            </svg>
            <!-- /ICON DOTS -->
          </p>
          <!-- /MENU MAIN ITEM LINK -->

          <!-- MENU MAIN -->
          <ul class="menu-main" style="top: -58px !important">

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
              <a class="menu-main-item-link" href="#">Marketplace</a>
              <!-- /MENU MAIN ITEM LINK -->
            </li>
            <!-- /MENU MAIN ITEM -->
          </ul>
          <!-- /MENU MAIN -->
        </li>
        <!-- /MENU MAIN ITEM -->
      </ul>
      <!-- /MENU MAIN -->
    </nav>
    <!-- /NAVIGATION -->
  </div>
  <!-- /BAR ACTIONS -->

  <!-- BAR ACTIONS -->
  <div class="bar-actions">
    <!-- ACTION LIST -->
    <div class="action-list dark">

       <!-- ACTION LIST ITEM -->
       <a class="action-list-item" href="hub-profile-messages.html">
          <!-- ACTION LIST ITEM ICON -->
          <svg class="action-list-item-icon icon-friend">
            <use xlink:href="#svg-friend"></use>
          </svg>
          <!-- /ACTION LIST ITEM ICON -->
        </a>
        <!-- /ACTION LIST ITEM -->

      <!-- ACTION LIST ITEM -->
      <a class="action-list-item" href="hub-profile-messages.html">
        <!-- ACTION LIST ITEM ICON -->
        <svg class="action-list-item-icon icon-messages">
          <use xlink:href="#svg-messages"></use>
        </svg>
        <!-- /ACTION LIST ITEM ICON -->
      </a>
      <!-- /ACTION LIST ITEM -->

      <!-- ACTION LIST ITEM -->
      <a class="action-list-item unread" href="hub-profile-notifications.html">
        <!-- ACTION LIST ITEM ICON -->
        <svg class="action-list-item-icon icon-notification">
          <use xlink:href="#svg-notification"></use>
        </svg>
        <!-- /ACTION LIST ITEM ICON -->
      </a>
      <!-- /ACTION LIST ITEM -->
    </div>
    <!-- /ACTION LIST -->

    <!-- ACTION ITEM WRAP -->
    <a class="action-item-wrap" href="javascript:void(0)" @click="changeapptheme(user_details.app_theme == 'light' ? 'dark' : 'light')">
      <!-- ACTION ITEM -->
      <div class="action-item dark">
        <i class="material-icons action-list-item-icon text-white" v-if="user_details.app_theme == 'light'">nights_stay</i>
        <i class="material-icons action-list-item-icon text-white" v-else>wb_sunny</i>
      </div>
      <!-- /ACTION ITEM -->
    </a>
    <!-- /ACTION ITEM WRAP -->
  </div>
  <!-- /BAR ACTIONS -->
</aside>
<!-- /FLOATY BAR -->

</inc-floatybar>
