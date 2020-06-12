(function(){var $oMasking;var $oWindowContainer;$.fn.openWindow=function(setTitle,setContents,setButton){var _html='<div class="window-masking"></div>'+
'<div class="window-container fix" id="addNew">'+
'<h2></h2>'+
'<div class="window-content">'+
'<p class="window-text"></p>'+
'</div>'+
'<div class="window-btn fix">'+
'<a class="cancel-button fl" href="javascript:;"></a>'+
'<a class="confirm-button fr" href="javascript:;"></a>'+
'<a class="ack-button fr" href="javascript:;"></a>'+
'</div>'+
'</div>';$('body').append(_html);$oMasking=$('.window-masking');$oWindowContainer=$('.window-container');$('.cancel-button,.window-masking,.ack-button').on('click',function(){closeWindow();});modal=new Modal();console.log(setButton+","+setContents+","+setButton)
modal.setTitle(setTitle);modal.setContents(setContents);modal.setButton(setButton);$oMasking.show();$oWindowContainer.show();}
function closeWindow(){$oMasking=$('.window-masking');$oWindowContainer=$('.window-container');$oMasking.remove();$oWindowContainer.remove();}
var Modal=function(){thismodal=$('#addNew');};Modal.prototype={setContents:function(obj){thismodal.find('p.window-text').html(obj);},setTitle:function(obj){if(obj!=""){thismodal.find('h2').show().html(obj);}},setButton:function(obj){console.log(obj)
var json=eval(obj);if(json.length==1){thismodal.find('a.ack-button').show().html(json[0]);}
if(json.length==2){thismodal.find('a.cancel-button').show().html(json[0]);thismodal.find('a.confirm-button').show().html(json[1]);}}}})()