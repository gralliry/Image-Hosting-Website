// import axios from 'axios';
Vue.config.productionTip = false;
// Vue.prototype.$axios = axios;
Vue.config.BaseUrl = window.location.hostname === "localhost" ? "https://image.forye.top" : window.location.origin;
new Vue({
    el: "#app",
    data: {
        // 主页面
        order: 1,
        isScrollBottom: false,
        stopGetPicture: false,
        pictures: [],
        isShowBackgroundPicture: false,
        backgroundPicture: {
            url: null,
            des: null,
            uid: null
        },
        isEmphasizePicture: false,
        // 上传窗口
        isShowUploadWindow: false,
        uploadData: {
            url: null,
            des: null,
            file: null
        },
        isUrlRight: false,
        isShowUploadWindowTip: false,
        tipData: {
            url: null,
            uid: null,
            key: null
        },
        // 删除窗口
        isShowDeleteWindow: false,
        deleteData: {
            uid: null,
            key: null,
        },
        // 展示遮罩
        isShowMaskingOut: false,
    },
    beforeMount() {
        for (let i = 2; i > 0; i--) {
            this.getPictures();
        }
    },
    methods: {
        backToTop() {
            let targetEl = this.$el.querySelector('article');
            let scrollTop = targetEl.scrollTop;
            if (scrollTop > 20) {
                window.requestAnimationFrame(() => this.backToTop());
                targetEl.scrollTop = scrollTop - scrollTop / 8;
            }
        },
        navShowContactWay() {
            if (confirm('To contact me\nE-mail: aiccyxixy@163.com\nCopy E-mail to clipboard?')) {
                this.copyToClipboard("aiccyxixy@163.com");
            }
        },
        // 接口
        getPictures() {
            if (this.stopGetPicture) return;
            let params = new URLSearchParams();
            params.append('order', this.order);
            this.order++;
            axios.post(Vue.config.BaseUrl + '/api/GetPictures.php', params)
                .then(response => {
                    // deal with response
                    let data = response['data'];
                    if (data['status'] !== 200) return alert('Get Pictures Error');
                    let pictureData = data['data'];
                    for (let picture of pictureData) {
                        this.pictures.push(picture);
                    }
                    this.order++;
                    this.stopGetPicture = (pictureData.length === 0);
                    this.scrollBottomStatue = false;
                })
                .catch(error => {
                    // deal with error
                    console.error(error);
                    alert('A Unknown Error');
                });
        },
        deletePicture() {
            let params = new URLSearchParams();
            params.append('uid', this.deleteData.uid);
            params.append('key', this.deleteData.key);
            // send POST request
            axios.post(Vue.config.BaseUrl + '/api/DeletePicture.php', params)
                .then(response => {
                    // deal with response
                    alert(response['data']['message']);
                    this.cancelDelete();
                })
                .catch(error => {
                    // deal with error
                    console.error(error);
                    alert('A Unknown Error');
                });
        },
        postPicture() {
            // Create FormData Object
            let formData = new FormData();
            // add file to FormData object
            formData.append('file', this.uploadData.file);
            formData.append('url', this.uploadData.url);
            formData.append('des', this.uploadData.des);
            // sent POST request
            axios.post(Vue.config.BaseUrl + '/api/PostPicture.php', formData)
                .then(response => {
                    // deal with response
                    let data = response['data'];
                    if (data['status'] !== 200) return alert(data['message']);
                    this.tipData.url = data['data']['url'];
                    this.tipData.uid = data['data']['uid'];
                    this.tipData.key = data['data']['key'];
                    this.isShowUploadWindowTip = true;
                    this.pictures.push({
                        url: data['data']['url'],
                        uid: data['data']['uid'],
                        des: this.uploadData.des,
                    });
                })
                .catch(error => {
                    // deal with error
                    console.error(error);
                    alert("A Unknown Error");
                });
        },
        // 工具流 // Copy text to clipboard
        copyToClipboard(text) {
            let inputDom = document.createElement('input');
            inputDom.setAttribute('readonly', 'readonly');
            inputDom.value = text;
            document.body.appendChild(inputDom);
            inputDom.select();
            // navigator.clipboard.writeText(text).then(() => alert("Copy successfully"));
            document.execCommand('copy');
            inputDom.style.display = 'none';
            inputDom.remove();
        },
        // 滑动动作
        scrollSlider(event) {
            if (this.stopGetPicture)
                return;
            let element = event.target;
            const scrollHeight = element.scrollHeight;
            const scrollTop = element.scrollTop;
            const offsetHeight = element.offsetHeight;
            // 判断是否到达底部
            if (!this.scrollBottomStatue && scrollTop + offsetHeight >= scrollHeight - 10) {
                this.scrollBottomStatue = true;
                this.getPictures();
            }
        },
        // 确认提示
        confirmTip() {
            this.isShowUploadWindow = false;
            this.isShowUploadWindowTip = false;
            this.copyToClipboard(
                'url: ' + this.tipData.url +
                '\nuid: ' + this.tipData.uid +
                '\nkey: ' + this.tipData.key
            );
            this.cancelUpload();
        },
        emphasizePicture(picture) {
            this.isEmphasizePicture = true;
            this.isShowBackgroundPicture = true;
            this.backgroundPicture.url = picture.url;
            this.backgroundPicture.des = picture.des;
            this.backgroundPicture.uid = picture.uid;
        },
        createUrlByFile(event) {
            let all_files = event.target.files;
            if (all_files.length > 0) {
                this.uploadData.file = all_files[0];
                this.uploadData.url = window.URL.createObjectURL(this.uploadData.file);
            }
        },
        closeDeleteWindow() {
            this.isShowDeleteWindow = false;
        },
        cancelDelete() {
            this.closeDeleteWindow();
            this.deleteData.uid = null;
            this.deleteData.key = null;
        },
        closeUploadWindow() {
            this.isShowUploadWindow = false;
        },
        cancelUpload() {
            this.closeDeleteWindow();
            this.isShowUploadWindowTip = false;
            this.uploadData.url = null;
            this.uploadData.des = null;
            this.uploadData.file = null;
        }
    },
})


