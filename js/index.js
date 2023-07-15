// import axios from 'axios';
Vue.config.productionTip = false;
// Vue.prototype.$axios = axios;
const nav = new Vue({
    el:'#nav',
    methods: {
        back_to_top(){
            var targetEl = main_section.$el.querySelector('article');
            var scrollTop = targetEl.scrollTop;
            if(scrollTop > 20){
                window.requestAnimationFrame(()=>this.back_to_top());
                targetEl.scrollTop = scrollTop - scrollTop / 8;
            }
        },
        popup_delete_picture(){
            delete_window.isShow = true;
            // masking_out.isShow = true;
        },
        popup_upload_picture(){
            upload_window.isShow = true;
            // masking_out.isShow = true;
        },
        show_about_me_info(){
            alert("Why don't you contact me and get to know me?\nI will be looking forward to it");
        },
        show_contact_me_info(){
            if(confirm('To contact me\nE-mail: aiccyxixy@163.com\nCopy E-mail to clipboard?'))
                copytoClipboard("aiccyxixy@163.com");
        }
    },
});

const main_section = new Vue({
    el:"#main_section",
    data:{
        order:1,
        scrollBottomStatu:false,
        stopGetPicture:false,
        pictures:[],
        isShow:false,
        backPicture:{
            url:null,
            des:null,
            num:null
        },
        isEmphasize:false
    },
    computed:{
        backStyle(){return {'flex':this.isShow?300:15};},
        emphasizePictureUrl(){return (this.isEmphasize?this.backPicture.url:null);}
    },
    beforeMount(){
        this.getPictures();
        this.getPictures();
    },
    methods:{
        scroll(event){
            if(this.stopGetPicture) return;
            var element = event.target;
            const scrollHeight = element.scrollHeight;
            const scrollTop = element.scrollTop;
            const offsetHeight = element.offsetHeight;
            // 判断是否到达底部
            if (!this.scrollBottomStatu && scrollTop + offsetHeight >= scrollHeight - 10) {
                this.scrollBottomStatu = true;
                this.getPictures();
            }
        },
        showPicture(picture){
            this.isEmphasize = this.isShow = true;
            this.backPicture.url = picture.url;
            this.backPicture.des = picture.des;
            this.backPicture.num = picture.num;
        },
        getPictures(){
            if(this.stopGetPicture) return;
            var params = new URLSearchParams();
            params.append('order', this.order);
            this.order++;
            axios.post('./api/GetPictures.php', params)
            .then(response => {
                // deal with response
                console.log(response.data);
                var data = response['data'];
                if(data['statu']!=200) return alert('Get Pictures Error');
                var pictureData = data['data'];
                for(let picture of pictureData){
                    this.pictures.push(picture);
                }
                this.order++;
                this.stopGetPicture = (pictureData.length==0);
                this.scrollBottomStatu = false;
            })
            .catch(error => {
                // deal with error
                console.log(error);
                alert('A Unknown Error');
            });        
        }
    }
});

const masking_out = new Vue({
    el:'#masking_out',
    data:{
        isShow:false
    },
});
const delete_window = new Vue({
    el:'#delete_window',
    data:{
        isShow:false,
        postData:{
            num:null,
            key:null,
        }
    },
    computed:{
        style(){return {'top':this.isShow?'50%':'-50%'}}
    },
    methods:{
        close(){
            this.isShow = false;
        },
        cancel(){
            this.close();
            this.postData.num = this.postData.key = null;
        },
        submit(){
            var params = new URLSearchParams();
            params.append('num', this.postData.num);
            params.append('key', this.postData.key);
            // send POST request
            axios.post('./api/DeletePicture.php', params)
            .then(response => {
                // 假设数组名为 myArray，要删除的元素的某个属性为 targetValue
                // main_section.pictures = main_section.pictures.filter(item => item.num !== this.postData.num);
                // deal with response
                var data = response['data'];
                alert(data['message']);
                this.cancel();
            })
            .catch(error => {
                // deal with error
                console.log(error);
                alert('A Unknown Error');
            });
        }
    }
});

const upload_window = new Vue({
    el:'#upload_window',
    data:{
        isShow:false,
        isUrlRight:true,
        formData:{
            url:null,
            des:null,
            file:null
        },
        showTip:false,
        showData:{
            url:null,
            num:null,
            key:null
        }
    },
    computed:{
        style(){return {'top':this.isShow?'50%':'-50%'}},
        iconClass(){return ((this.isUrlRight||!this.formData.url)?'icon-qietu-xuanzetupian':'icon-jiazaicuowu')},
        tipStyle(){return {'display':this.showTip?'flex':'none'}}
    },
    methods:{
        create_url_by_file(event){
            var allfile = event.target.files;
            if(allfile.length>0){
                this.formData.file = allfile[0];
                this.formData.url = window.URL.createObjectURL(this.formData.file);
            }
        },
        handleLoad(){
            this.isUrlRight = true;
        },
        handleError(){
            this.isUrlRight = false;
        },
        close(){
            this.isShow = false;
        },
        cancel(){
            this.close();
            this.formData.url = this.formData.file = this.formData.des = null;
            this.showTip = false;
        },
        submit(){
            // Create FormData Object
            var formData = new FormData();
            // add file to FormData object
            formData.append('file', this.formData.file);
            formData.append('url', this.formData.url);
            formData.append('des', this.formData.des);
            // sent POST request
            axios.post('./api/PostPicture.php', formData)
            .then(response => {
                // deal with response
                var data = response['data'];
                console.log(response);
                if(data['statu']!=200) return alert(data['message']);
                this.showData.url = data['data']['url'];
                this.showData.num = data['data']['num'];
                this.showData.key = data['data']['key'];
                this.showTip = true;
                main_section.pictures.push({
                    url:data['data']['url'],
                    num:data['data']['num'],
                    des:this.formData.des,
                });
            })
            .catch(error => {
                // deal with error
                console.log(error);
                alert("A Unknown Error");
            });
        },
        confirm(){
            this.isShow = this.showTip = false;
            copytoClipboard(
                'url: '  + this.showData.url +
                ' num: ' + this.showData.num +
                ' key: ' + this.showData.key
            );
            this.cancel();
        }
    }
});

masking_out.$watch(function(){
    return delete_window.isShow||upload_window.isShow||main_section.isEmphasize;
},function(newValue){
    masking_out.isShow = newValue;
});

document.getElementById("loading_making_out").remove();
//Copy text to clipboard
function copytoClipboard(text){
    var inputDom = document.createElement('input');
    inputDom.setAttribute('readonly', 'readonly');
    inputDom.value = text;
    document.body.appendChild(inputDom);
    inputDom.select();
    document.execCommand('Copy');
    inputDom.style.display = 'none';
    inputDom.remove();
}