webpackJsonp([19],{"4Uo3":function(t,i,n){"use strict";function e(t){n("NSHz")}Object.defineProperty(i,"__esModule",{value:!0});var s=n("Ym9J"),o=n.n(s),A=n("8Llf"),l={mixins:[A.a],data:function(){return{data_uri:"review_students",data:[]}},mounted:function(){},methods:{formatDate:function(t){return o()(t).startOf("day").fromNow()},redirect:function(t){this.$router.push({path:"/reviews/"+t.rs_id})}}},m=function(){var t=this,i=t.$createElement,n=t._self._c||i;return n("div",[n("div",{directives:[{name:"me-scroll",rawName:"v-me-scroll"}],staticClass:"mescroll",style:{top:t.headerHeight+"px"}},[n("div",{ref:"dataList"},t._l(t.data,function(i,e){return n("div",{key:e,staticClass:"list-img-item",on:{click:function(n){t.redirect(i)}}},[n("div",{staticClass:"list-item-img avatar-xs"},[n("img",{attrs:{src:i.employee.photo_url?i.employee.photo_url:"http://s1.xiao360.com/common_img/avatar.jpg",onerror:"this.src='http://s1.xiao360.com/common_img/avatar.jpg'"}})]),t._v(" "),n("div",{staticClass:"list-item-content"},[n("div",{staticClass:"list-item-title"},[t._v("\n\t\t\t\t\t\t"+t._s(i.employee.ename)+"\n\t\t\t\t\t\t"),n("span",{staticClass:"list-item-time"},[t._v(t._s(t.formatDate(i.create_time)))])]),t._v(" "),n("div",{staticClass:"list-item-desc"},[t._v(t._s(i.detail))])])])}))])])},r=[],a={render:m,staticRenderFns:r},c=a,p=n("8AGX"),C=e,d=p(l,c,!1,C,null,null);i.default=d.exports},NSHz:function(t,i,n){var e=n("SzXM");"string"==typeof e&&(e=[[t.i,e,""]]),e.locals&&(t.exports=e.locals);n("8bSs")("66bac487",e,!0)},SzXM:function(t,i,n){i=t.exports=n("BkJT")(!0),i.push([t.i,"\n.mescroll {\n  top: 0;\n  bottom: 0;\n  height: auto;\n  position: fixed;\n}\n.list-img-item {\n  padding: 13px 13px;\n  background: #fff;\n  position: relative;\n}\n.list-img-item:after {\n  content: '';\n  width: calc(100% - 60px);\n  height: 1px;\n  border-bottom: 1px solid #EEEEEE;\n  position: absolute;\n  bottom: 0;\n  right: 0;\n}\n.list-img-item .list-item-img {\n  display: inline-block;\n  width: 40px !important;\n  margin-right: 10px;\n}\n.list-img-item .list-item-img img {\n  border-radius: 50%;\n}\n.list-img-item .list-item-content {\n  display: inline-block;\n  width: calc(100% - 60px);\n}\n.list-img-item .list-item-title {\n  font-size: 15px;\n  color: #323232;\n  line-height: 30px;\n}\n.list-img-item .list-item-time {\n  float: right;\n  font-size: 12px;\n  color: #999999;\n}\n.list-img-item .list-item-desc {\n  white-space: nowrap;\n  text-overflow: ellipsis;\n  overflow: hidden;\n  font-size: 12px;\n  color: #999999;\n}\n","",{version:3,sources:["/Users/payhon/Project/x360p/src/x360p_student_mobile/src/views/home/reviews.vue"],names:[],mappings:";AACA;EACE,OAAO;EACP,UAAU;EACV,aAAa;EACb,gBAAgB;CACjB;AACD;EACE,mBAAmB;EACnB,iBAAiB;EACjB,mBAAmB;CACpB;AACD;EACE,YAAY;EACZ,yBAAyB;EACzB,YAAY;EACZ,iCAAiC;EACjC,mBAAmB;EACnB,UAAU;EACV,SAAS;CACV;AACD;EACE,sBAAsB;EACtB,uBAAuB;EACvB,mBAAmB;CACpB;AACD;EACE,mBAAmB;CACpB;AACD;EACE,sBAAsB;EACtB,yBAAyB;CAC1B;AACD;EACE,gBAAgB;EAChB,eAAe;EACf,kBAAkB;CACnB;AACD;EACE,aAAa;EACb,gBAAgB;EAChB,eAAe;CAChB;AACD;EACE,oBAAoB;EACpB,wBAAwB;EACxB,iBAAiB;EACjB,gBAAgB;EAChB,eAAe;CAChB",file:"reviews.vue",sourcesContent:["\n.mescroll {\n  top: 0;\n  bottom: 0;\n  height: auto;\n  position: fixed;\n}\n.list-img-item {\n  padding: 13px 13px;\n  background: #fff;\n  position: relative;\n}\n.list-img-item:after {\n  content: '';\n  width: calc(100% - 60px);\n  height: 1px;\n  border-bottom: 1px solid #EEEEEE;\n  position: absolute;\n  bottom: 0;\n  right: 0;\n}\n.list-img-item .list-item-img {\n  display: inline-block;\n  width: 40px !important;\n  margin-right: 10px;\n}\n.list-img-item .list-item-img img {\n  border-radius: 50%;\n}\n.list-img-item .list-item-content {\n  display: inline-block;\n  width: calc(100% - 60px);\n}\n.list-img-item .list-item-title {\n  font-size: 15px;\n  color: #323232;\n  line-height: 30px;\n}\n.list-img-item .list-item-time {\n  float: right;\n  font-size: 12px;\n  color: #999999;\n}\n.list-img-item .list-item-desc {\n  white-space: nowrap;\n  text-overflow: ellipsis;\n  overflow: hidden;\n  font-size: 12px;\n  color: #999999;\n}\n"],sourceRoot:""}])}});
//# sourceMappingURL=19.4830b9feaa41a792baab.js.map