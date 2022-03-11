strawberry.create("app", function () {
  setTimeout(function () {
    $(".loading").fadeOut();
    $("#loader").html("");
    $("#main").fadeIn();
  },2000);
});

app.factory('grootSvc',function(){
  return {
    //root: 'https://grootsvcapis.com/api/',
    root:'http://localhost/api/',
    publicKey: 'm3Ne22u7es3uT7p5az8S2PUNnX3KDw'
  }
});



app.factory('requester',function(){
  return {
    isOnline:function(){return null==localStorage.getItem('ractive')?'offline':localStorage.getItem('ractive')},
    setOnline:function(){
        localStorage.setItem('ractive','online');
        let d = new Date();
        localStorage.setItem('rexp',(Math.round(d.getTime()/60000))+20);
    },
    setOffline:function(){
        localStorage.setItem('ractive','offline');
        localStorage.setItem('rtoken','public');
    },
    setToken:function(token){
        localStorage.setItem('rtoken',token);
    },
    setName:function(firstName,lastName){
        localStorage.setItem('rname',firstName+' '+lastName);
    },
    getName:function(){
      return null==localStorage.getItem('rname')?'':localStorage.getItem('rname')
    },
    setPhoto:function(url){
      localStorage.setItem('rphoto',url);
    },
    getPhoto:function(){
      if (localStorage.getItem('rphoto')=='null') {
          return '/images/empty-profile.png';
      }
      return localStorage.getItem('rphoto');
    },
    setUsername:function(username){
        localStorage.setItem('rusername',username);
    },
    getUsername:function(){
      return null==localStorage.getItem('rusername')?'':localStorage.getItem('rusername')
    },
    getToken:function(){
        return null==localStorage.getItem('rtoken')?'invalidated':localStorage.getItem('rtoken')
    },
    createExpiryAt:function(){
        let d = new Date();
        localStorage.setItem('rexp',(Math.round(d.getTime()/60000))+20);
    },
    getExpiryAt:function(){
        return null==localStorage.getItem('rexp')?0:localStorage.getItem('rexp');
    }
  }
});



app.service("errorHandler", function ($scope, $patch) {
  return {
    show: function (message) {
      $scope.hasError = true;
      $scope.errorMessage = message;
      $patch("formErrors");
    },
    clear: function () {
      $scope.hasError = false;
      $scope.errorMessage = "";
      $patch("formErrors");
    },
  };
});

app.service("validator", ($scope) => {
  return {
    forms: {
      firstName:function(){
        var regex = /^[a-zA-Z ]*$/;
        if ($scope.forms.firstName.length < 3) {
          return false;
        }
        if ($scope.forms.firstName.length > 32) {
          return false;
        }
        if (!regex.test($scope.forms.firstName)) {
          return false;
        }
        return true;
      },
      lastName:function(){
        var regex = /^[a-zA-Z ]*$/;
        if ($scope.forms.lastName.length < 2) {
          return false;
        }
        if ($scope.forms.lastName.length > 32) {
          return false;
        }
        if (!regex.test($scope.forms.lastName)) {
          return false;
        }
        return true;
      },
      username:function(){
        var regex = /^[a-zA-Z0-9]*$/;
        if ($scope.forms.username.length < 5) {
          return false;
        }
        if ($scope.forms.username.length > 32) {
          return false;
        }
        if (!regex.test($scope.forms.username)) {
          return false;
        }
        return true;
      },
      email:function(){
        var regex = /^[a-zA-Z0-9@.]*$/;
        if ($scope.forms.email.length < 8) {
          return false;
        }
        if ($scope.forms.email.length > 64) {
          return false;
        }
        if (!$scope.forms.email.includes('@')){
          return false;
        }
        if (!$scope.forms.email.includes('.')){
          return false;
        }
        if (!regex.test($scope.forms.email)) {
          return false;
        }
        return true;
      },
      password:function(){
        if ($scope.forms.password.length < 8) {
          return false;
        }
        if ($scope.forms.password.length > 64) {
          return false;
        }
        return true;
      }
    },
  };
});

app.factory('app',(requester,grootSvc)=>{
    return {
        local: 'http://127.0.0.1:8000/',
        ready:()=>{
            setTimeout(()=>{
                if (undefined==window.pageLoadOk&&window.pageLoadOk!==true) {
                    $('.layer#spinner').html('');
                    $('.layer#main').fadeIn();
                    window.pageLoadOk = true;
                    // Token Refresher
                    setInterval(()=>{
                        let d = new Date();
                        let cur = Math.round(d.getTime()/60000);
                        let expiryAt = requester.getExpiryAt();
                        if (cur>expiryAt||cur==expiryAt||cur<(expiryAt+2)) {
                            $.ajax({
                                url: grootSvc.root+'v1/users/get/token?token='+requester.getToken(),
                                contentType : 'application/json',
                                type : 'GET',
                                success: (response)=>{
                                    requester.setToken(response.token);
                                    requester.createExpiryAt();
                                },
                                error:(error)=>{
                                    location.href='/login.html';
                                }
                            });
                        }
                    },300000);
                }
            },500);
        }
    }
});
app.factory('url',function(){
    return {
        get:function(requester){
            let url = location.href;
            let name = requester.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
            var regexS = "[\\?&]"+name+"=([^&#]*)";
            var regex = new RegExp( regexS );
            var results = regex.exec( url );
            return results == null ? null : results[1];
        }
    }
});

app.factory('postModel',function(){
  class PostModel {
    constructor(post){
      this.content = post.content;
    }
  }
  return new PostModel;
});
