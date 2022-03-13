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
        full:'<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill:#009b84; transform: ;msFilter:;"><path d="M21.947 9.179a1.001 1.001 0 0 0-.868-.676l-5.701-.453-2.467-5.461a.998.998 0 0 0-1.822-.001L8.622 8.05l-5.701.453a1 1 0 0 0-.619 1.713l4.213 4.107-1.49 6.452a1 1 0 0 0 1.53 1.057L12 18.202l5.445 3.63a1.001 1.001 0 0 0 1.517-1.106l-1.829-6.4 4.536-4.082c.297-.268.406-.686.278-1.065z"></path></svg>',
        half:'<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill:#009b84; transform: ;msFilter:;"><path d="M5.025 20.775A.998.998 0 0 0 6 22a1 1 0 0 0 .555-.168L12 18.202l5.445 3.63a1.001 1.001 0 0 0 1.517-1.106l-1.829-6.4 4.536-4.082a1 1 0 0 0-.59-1.74l-5.701-.454-2.467-5.461a.998.998 0 0 0-1.822-.001L8.622 8.05l-5.701.453a1 1 0 0 0-.619 1.713l4.214 4.107-1.491 6.452zM12 5.429l2.042 4.521.588.047h.001l3.972.315-3.271 2.944-.001.002-.463.416.171.597v.003l1.253 4.385L12 15.798V5.429z"></path></svg>',
        empty:'<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill:#dfdfdf; transform: ;msFilter:;"><path d="M21.947 9.179a1.001 1.001 0 0 0-.868-.676l-5.701-.453-2.467-5.461a.998.998 0 0 0-1.822-.001L8.622 8.05l-5.701.453a1 1 0 0 0-.619 1.713l4.213 4.107-1.49 6.452a1 1 0 0 0 1.53 1.057L12 18.202l5.445 3.63a1.001 1.001 0 0 0 1.517-1.106l-1.829-6.4 4.536-4.082c.297-.268.406-.686.278-1.065z"></path></svg>'
      },
      review:'<svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="2 2 20 20" style="fill:#009b84; transform: ;msFilter:;"><path d="M20 2H4c-1.103 0-2 .897-2 2v12c0 1.103.897 2 2 2h3v3.766L13.277 18H20c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2zm0 14h-7.277L9 18.234V16H4V4h16v12z"></path><circle cx="15" cy="10" r="2"></circle><circle cx="9" cy="10" r="2"></circle></svg>'
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
    publicKey:'eg2n22JrBi2WfEr5L3jsRviuseZJ61',
    app:{
      root:'http://localhost/api/',
      uploads:{
        images: 'http://127.0.0.1:8000/api/push'
      }
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

app.service('PostModel',function($patch,grootSvc){
  class PostModel {
    constructor(post){
      this.token = post.token ?? 'public';
      this.content = post.content ?? "What's on your mind?";
      this.createdAt = post.createdAt ?? '';
      this.userId = post.userId ?? '';
      this.photo = post.photo ?? null;
      this.visibility = post.visibility ?? 'public';
    }
    submit(){
      this.content = $('[xpost-model="content"]').val();
      if (this.content.trim().length>0) {
        console.log(this);
        $.ajax({
          method:'POST',
          url:grootSvc.app.root+'v1/posts/create',
          data:JSON.stringify({
              token: this.token,
              postTitle: '',
              postBody: this.content,
              visibility: this.visibility,
              photo: this.photo
          }),
          success:function(response){
              location.reload();
          },
          error:function(response){
              console.log(response);
          }
        });
      }

    }
    updateVisibility(){
      $patch('post.visibility.icon');
    }
  }
  return PostModel;
});

app.factory('UserModel',function(){
  class UserModel {
    constructor(user){
        this.username = user.username;
        this.firstName = user.firstName;
        this.lastName = user.lastName;
        this.profilePhoto = user.profilePhoto;
        this.bottomLine = user.bottomLine;
        this.posts = user.posts;
        this.hasPost = user.hasPost ?? false;
    }
  }
  return UserModel;
});

app.service('pageSvc',function($scope,$patch){
  return {
    errors: {
      has:false,
      message: '',
      show:function(message){
        $scope.pageSvc.errors.has = true;
        $scope.pageSvc.errors.message = message;
        $patch('pageError');
      }
    }
  }
});

app.service('reviewSvc',function(presets){
  let generator=function(average){
    if (average!==undefined) {
      let floor = Math.floor(average);
      let template = '';
      let fullStar = presets.icons.stars.full;
      let halfStar = presets.icons.stars.half;
      let emptyStar = presets.icons.stars.empty;
      for (var i = 1; i < 6; i++) {
          if (i<floor+1) {
              template = template+fullStar;
          } else {
              let dec = 5-average;
              if (dec!==0&&dec<1) {
                  template = template+halfStar;
              } else {
                  template = template+emptyStar;
              }
          }
      }
      return template;
    }
    return '';
  }

  return {
    generateStars:function(average){
      console.log(average);
      return generator(average);
    },
    roundOff:function(average){
      if (average==undefined) return '0.0';
      if (average===0) return '0.0';
      return average.toFixed(2);
    },
    calculateStars:function(totalReviewsCount,totalReviewsScore){
        let average = totalReviewsScore / totalReviewsCount;
        let averageInDecimal = '0.0';
        if (average>0) {
            averageInDecimal = average.toFixed(1);
        }
        return '<div class="post-card-star-icons">'+generator(average)+'</div><div class="post-card-ave-score">'+averageInDecimal+'</div>';
    }
  }
});
