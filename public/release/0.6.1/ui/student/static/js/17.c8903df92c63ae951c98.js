webpackJsonp([17],{bSN1:function(n,t,e){var i=e("lrze");"string"==typeof i&&(i=[[n.i,i,""]]),i.locals&&(n.exports=i.locals);e("8bSs")("460605b7",i,!0)},"cZ+R":function(n,t,e){"use strict";function i(n){e("bSN1")}Object.defineProperty(t,"__esModule",{value:!0});var a=e("8Llf"),o={mixins:[a.a],data:function(){return{data:{}}},mounted:function(){this.init_data()},methods:{init_data:function(){var n=this;this.$rest("broadcasts/"+this.$route.params.id).get().success(function(t){console.log(t),n.data=t})}}},s=function(){var n=this,t=n.$createElement,e=n._self._c||t;return e("div",{staticClass:"detail-container"},[e("div",{staticClass:"title"},[n._v("\n\t\t"+n._s(n.data.title)+"\n\t")]),n._v(" "),e("p",{staticClass:"mg-t-10 desc"},[n._v(n._s(n.data.create_time))]),n._v(" "),e("div",{staticClass:"detail-body",domProps:{innerHTML:n._s(n.data.desc)}})])},r=[],c={render:s,staticRenderFns:r},d=c,l=e("8AGX"),A=i,p=l(o,d,!1,A,null,null);t.default=p.exports},lrze:function(n,t,e){t=n.exports=e("BkJT")(!0),t.push([n.i,"\n.detail-container {\n  padding: 13px 13px;\n  background: #fff;\n}\n.detail-container .title {\n  font-size: 21px;\n  font-weight: bold;\n}\n.detail-container .desc {\n  font-size: 12px;\n  color: #999999;\n}\n.detail-container .detail-body p {\n  color: #666666 !important;\n  font-size: 14px;\n}\n","",{version:3,sources:["/Users/payhon/Project/x360p/src/x360p_student_mobile/src/views/home/new/detail.vue"],names:[],mappings:";AACA;EACE,mBAAmB;EACnB,iBAAiB;CAClB;AACD;EACE,gBAAgB;EAChB,kBAAkB;CACnB;AACD;EACE,gBAAgB;EAChB,eAAe;CAChB;AACD;EACE,0BAA0B;EAC1B,gBAAgB;CACjB",file:"detail.vue",sourcesContent:["\n.detail-container {\n  padding: 13px 13px;\n  background: #fff;\n}\n.detail-container .title {\n  font-size: 21px;\n  font-weight: bold;\n}\n.detail-container .desc {\n  font-size: 12px;\n  color: #999999;\n}\n.detail-container .detail-body p {\n  color: #666666 !important;\n  font-size: 14px;\n}\n"],sourceRoot:""}])}});
//# sourceMappingURL=17.c8903df92c63ae951c98.js.map