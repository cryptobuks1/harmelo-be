import constant from './Constant.js';
class Toast {
    constructor() {
      if (!Toast.instance) {
        Toast.instance = this;
      }
      return Toast.instance;
    }
    sweetAlert(self, type, title, text) {
      self.$swal({
        type: type,
        title: title,
        text: text,
        icon: 'success',
      });
    }
    _success(self, title, text) {
      self.$swal({
        icon: 'success',
        title: title,
        text: text
      });
    }
    _successRedirect(self, title, text, target_location) {
        self.$swal({
          icon: 'success',
          title: title,
          text: text
        }).then((res) => {
            if (res.isConfirmed)
                window.location.href = '/' + target_location;
        });
      }
    _error(self, title, text) {
      self.$swal({
        icon: 'error',
        title: title,
        text: text
      });
    }
    _warning(self, title, text) {
      self.$swal({
        icon: 'warning',
        title: title,
        text: text
      });
    }
}
const instance = new Toast();
Object.freeze(instance);
export default instance;

