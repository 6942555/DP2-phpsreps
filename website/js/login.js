var app = angular.module("phpsreps", []);
app.controller ("myCtrl", function ($scope){
  /*code here*/

  $scope.login = function(){
    if($scope.uName == 'admin' && $scope.pWord == 'password'){
      window.location.href = "main.html";
    }else{
      $scope.errMsg = "Wrong username or password!";
    }
  }

  $scope.delete = function(item){
    var i = $scope.posts.indexOf(item);
    $scope.posts.splice(i, 1);
    $scope.numPosts--;
  }

}
);

