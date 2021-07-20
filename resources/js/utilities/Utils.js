import constant from './Constant.js';

class Utils {

    constructor() {
        if (!Utils.instance) {
          Utils.instance = this;
        }
        return Utils.instance;
      }

    _getWeb(method) {
        return constant.web_local + method;
        //return constant.web_live + method;
    }

    _getApi(method) {
        return constant.api_local + method;
        //return constant.api_live + method;
    }

    _swalV1(title,text, show_cancel, icon) {
        return {
          title: title,
          html: text,
          icon: icon,
          width: '30em',
          allowEscapeKey: false,
          reverseButtons: true,
          showLoaderOnConfirm: true,
          showCancelButton: show_cancel,
          allowOutsideClick: false,
          cancelButtonText: 'Cancel',
          confirmButtonText: 'OK'
        }
    }

    _swalV2(title,text, show_cancel, icon) {
        return {
          title: title,
          html: text,
          icon: icon,
          width: '30em',
          allowEscapeKey: false,
          reverseButtons: true,
          showLoaderOnConfirm: true,
          showCancelButton: show_cancel,
          allowOutsideClick: false,
          cancelButtonText: 'Cancel',
          confirmButtonText: 'Continue'
        }
    }

    _getMD5(str) {
        var md5 = require('md5');

        return md5(str);
    }

}

const instance = new Utils();
Object.freeze(instance);
export default instance;
