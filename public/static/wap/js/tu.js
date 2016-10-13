/* 瀑布流 图片预加载*/
var imgReady = (function () {
var list = [], intervalId = null,
tick = function () {
var i = 0;
for (; i < list.length; i++) {
list[i].end ? list.splice(i--, 1) : list[i]();
};
!list.length && stop();
},
stop = function () {
clearInterval(intervalId);
intervalId = null;
};
return function (url, ready, load, error) {
var onready, width, height, newWidth, newHeight,
img = new Image();    
img.src = url;
if (img.complete) {
ready.call(img);
load && load.call(img);
return;
};
width = img.width;
height = img.height;
img.onerror = function () {
error && error.call(img);
onready.end = true;
img = img.onload = img.onerror = null;
};
onready = function () {
newWidth = img.width;
newHeight = img.height;
if (newWidth !== width || newHeight !== height ||
newWidth * newHeight > 1024
) {
ready.call(img);
onready.end = true;
};
};
onready();
img.onload = function () {
!onready.end && onready();    
load && load.call(img);
img = img.onload = img.onerror = null;
};
if (!onready.end) {
list.push(onready);
if (intervalId === null) intervalId = setInterval(tick, 40);
};
};
})();
var JwaterFall = document.getElementById('main'),
Jmore = document.getElementById('Jmore'),
//JwaterFallLeft = JwaterFall.getElementsByTagName('ul')[0],
//JwaterFallRight = JwaterFall.getElementsByTagName('ul')[1],
scrollNo = 1,
pNo = 1,
oClick = false;
var waterFallData = {
index : 0,
loopNum : 0,
errorNum : 0,
unloadNum : 0,
//heightLeft : JwaterFallLeft.clientHeight,
//heightRight : JwaterFallRight.clientHeight,
heightJson : []
}
function append (data,num,pageCount) {
function appendjson() {
if (waterFallData.loopNum + waterFallData.errorNum +waterFallData.unloadNum == num) {
for (var i = 0 , len=waterFallData.heightJson.length; i < len ; i++) {
JwaterFall.appendChild(waterFallData.heightJson[i]);
//waterFallData.heightLeft = JwaterFallLeft.clientHeight;
}
waterFallData.heightJson = [];
window.addEventListener("resize",function () {
//waterFallData.heightRight = JwaterFallRight.clientHeight;
//waterFallData.heightLeft = JwaterFallLeft.clientHeight;
})
waterFallData.loopNum = 0;
waterFallData.errorNum = 0;
if (pNo >= pageCount) {
oClick = false;
Jmore.innerHTML = "没有啦！";
return;
}else if(scrollNo < 3){
oClick = true;
Jmore.innerHTML = "<a>向上滑动 查看更多<i></i></a>";
}else{
oClick = true;
Jmore.innerHTML = "<a>点击查看更多<i></i></a>";
}   
}
}
if(!data[waterFallData.index]){
waterFallData.index=0;
}
var jsonSrc = data[waterFallData.index].image,
jsonUrl = data[waterFallData.index].url,
jsonTitle = data[waterFallData.index].title;
jsonDesc = data[waterFallData.index].description;
jsonDate = data[waterFallData.index].date;
imgReady(jsonSrc, function () {
if (this.width != 0) {
var liEle = document.createElement("li");
liEle.innerHTML = '<a href="' + jsonUrl + '"><dl><dt><img src="' + jsonSrc + '"  /></dt><dd><h2>'+jsonTitle+'</h2><time>'+jsonDate+'</time><p>'+jsonDesc+'</p></dd></a>';
waterFallData.heightJson.push(liEle);
}
waterFallData.loopNum++;
appendjson();
},function () {
appendjson();
},function () {
waterFallData.errorNum++;
appendjson();
});     
waterFallData.index++;
};
function callback(data) {
var articleList = data.articleList,
leng = 10,
pageCount = Math.ceil(articleList.length/leng);
pNo = 1 ;
waterFallData.index = 0;
Jmore.innerHTML = "正在加载...";
function showdata(articleList,leng,pageCount){    
for (var i = 0 , len=leng; i < len ; i++) {
append (articleList,leng,pageCount);
}
};
showdata(articleList,leng,pageCount);
window.addEventListener("scroll",function () {
if(scrollNo < 3){
var a = document.documentElement.clientHeight;
var c = document.documentElement.scrollHeight;
var b = document.documentElement.scrollTop + document.body.scrollTop;
if ( b >= (c - a - 110) && oClick) {
Jmore.innerHTML = "正在加载...";
oClick = false;
pNo++;
scrollNo++;
showdata(articleList,leng,4);
}else{
return false;
}
}else{
Jmore.onclick = function () {
if(oClick && scrollNo == 3){
oClick = false;
this.innerHTML = "正在加载...";
pNo++;
scrollNo++;
getDetails();
}else if(oClick && scrollNo > 3){
oClick = false;
this.innerHTML = "正在加载...";
pNo++;
scrollNo++;
showdata(articleList,leng,pageCount);
}else{
Jmore.onclick = null;
}
};  
}   
})
}
function getScript(url) {
var scr = document.createElement('script');
scr.src = url;
scr.charset = "utf-8";
document.body.insertBefore(scr, document.body.firstChild);
}
function getDetails() {
var url = "code/index.php";
getScript(url);
} 
