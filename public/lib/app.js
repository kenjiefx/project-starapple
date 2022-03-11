strawberry.create('app',function(){
  setTimeout(function(){
    $(".loading").fadeOut();
    $("#loader").html("");
    $("#main").fadeIn();
  },2000);
});

app.factory('presets',function(){
  return {
    icons: {
      emptyProfilePhoto:'/images/empty-profile.png',
      stars:{
        full:'<svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="2 2 20 20" style="transform: ;msFilter:;"><path d="M21.947 9.179a1.001 1.001 0 0 0-.868-.676l-5.701-.453-2.467-5.461a.998.998 0 0 0-1.822-.001L8.622 8.05l-5.701.453a1 1 0 0 0-.619 1.713l4.213 4.107-1.49 6.452a1 1 0 0 0 1.53 1.057L12 18.202l5.445 3.63a1.001 1.001 0 0 0 1.517-1.106l-1.829-6.4 4.536-4.082c.297-.268.406-.686.278-1.065z"></path></svg>'
      }
    },
    routes:{
      unAuth: {
        landingPage: '/login.html'
      }
    }
  }
});

/**
 * Register all your app components here
 */
app.factory('components',function(){
  return {
    deletePost:'/c/delete-post.htm',
    header: '/c/header.htm',
    profileCard:'/c/profile-card.htm'
  }
});

app.factory('grootSvc',function(){
  return {
    publicKey:'m3Ne22u7es3uT7p5az8S2PUNnX3KDw',
    app:{
      root:'http://localhost/api/'
    }
  }
});

app.factory('Requester',function(presets){
  class RequesterModel {
    constructor(){
      this.refresh();
    }
    set(requester){
      void 0!==requester.status&&localStorage.setItem("ractive",requester.status);
      void 0!==requester.name&&localStorage.setItem("rname",requester.name);
      void 0!==requester.token&&localStorage.setItem("rtoken",requester.token);
      void 0!==requester.username&&localStorage.setItem("rusername",requester.username);
      void 0!==requester.photo&&localStorage.setItem("rphoto",requester.photo);
      this.refresh();
    }
    refresh(){
      this.status = null==localStorage.getItem('ractive')?'offline':localStorage.getItem('ractive');
      this.name = null==localStorage.getItem('rname')?'':localStorage.getItem('rname');
      this.token = null==localStorage.getItem('rtoken')?'public':localStorage.getItem('rtoken');
      this.username = null==localStorage.getItem('rusername')?'':localStorage.getItem('rusername');
      this.profilePhoto = presets.emptyProfilePhoto;
      if (localStorage.getItem('rphoto')!=='null') {
          this.profilePhoto = localStorage.getItem('rphoto');
      }
    }
    signOut(){
      this.set({
        status: 'offline',
        name:'',
        token:'public',
        username:'',
        photo:presets.emptyProfilePhoto
      });
    }
    checkStatus(){
      if (this.status=='offline') {
        location.href=presets.routes.unAuth.landingPage;
      }
    }
  }
  return new RequesterModel;
});

/**
* Modal Controllers
* In order to work, add xpatch="modalCtrl" attribute to the modal controller
* @requires jQuery to work
**/
app.service('modalCtrl',function($scope,$patch){
  class ModalCtrl {
    constructor(){
      this.view = null;
    }
    open(view){
      this.renderView(view).modalElement().fadeIn();
    }
    close(){
      this.renderView(null).modalElement().fadeOut();
    }
    modalElement(){
      return $('[xpatch="modalCtrl"]');
    }
    renderView(view){
      this.view = view;
      $patch('modalCtrl');
      return this;
    }
  }
  return new ModalCtrl;
});

/**
 * Utility Services
 * @requires moment.js
 */
app.service('utilSvc',function(){
  return {
    pluralize:function(count,singular,plural){
      if (count==1) return singular;
      return plural;
    },
    date:{
      format:function(date){
        return moment(date).format('MMMM D, YYYY');
      }
    },
    queryParams:{
      get:function(key){
        let name = key.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
        var regex = new RegExp("[\\?&]"+name+"=([^&#]*)");
        var results = regex.exec(location.href);
        return results == null ? null : results[1];
      }
    }
  }
});

app.factory('PostModel',function(){
  class PostModel {
    constructor(post){
      this.content = post.content;
      this.createdAt = post.createdAt;
      this.userId = post.userId;
    }
  }
  return PostModel;
});

app.factory('UserModel',function(){
  class UserModel {
    constructor(user){

    }
  }
  return UserModel;
});
