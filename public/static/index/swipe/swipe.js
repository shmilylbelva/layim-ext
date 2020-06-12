    function swipe(){
      var expansion = null; //是否存在展开的list
      var container = document.querySelectorAll('.layim-list-history li .left');
      for(var i = 0; i < container.length; i++){ 
        var x, y, X, Y, swipeX, swipeY;
        container[i].addEventListener('touchstart', function(event) {
          x = event.changedTouches[0].pageX;
          y = event.changedTouches[0].pageY;
          swipeX = true;
          swipeY = true ;
          if(expansion){ //判断是否展开，如果展开则收起
            expansion.parentNode.classList.remove("swipeleft");
              if ($(this).parent().attr('touch_method') == 'swipe') {
                $(this).parent().attr('touch_method','');
              }            
          }  
        });
        container[i].addEventListener('touchmove', function(event){
          X = event.changedTouches[0].pageX;
          Y = event.changedTouches[0].pageY;  
                
          // 左右滑动
          if(swipeX && Math.abs(X - x) - Math.abs(Y - y) > 0){
            // 阻止事件冒泡
            event.stopPropagation();
            if(X - x > 10){ //右滑
              event.preventDefault();
              if ($(this).parent().attr('touch_method') == undefined || $(this).parent().attr('touch_method') == '') {
                $(this).parent().attr('touch_method','swipe');
              }
              // this.classList.remove("swipeleft"); //右滑收起
              this.parentNode.getAttribute("class").replace("swipeleft"," "); //右滑收起
            }
            if(x - X > 10){ //左滑
              event.preventDefault();
              if ($(this).parent().attr('touch_method') == undefined || $(this).parent().attr('touch_method') == '') {
                $(this).parent().attr('touch_method','swipe');
              }              
              this.parentNode.classList.add("swipeleft"); //左滑展开
              expansion = this;
            }
            swipeY = false;
          }
          // 上下滑动
          if(swipeY && Math.abs(X - x) - Math.abs(Y - y) < 0) {
            swipeX = false;
          }  
        });
      } 
    }  