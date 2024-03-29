import{aw as I,ax as u,ay as C,az as V,aA as O,aB as P,aC as S,aD as T,aE as x,aF as b}from"./index-c5917c64.js";const R=function(s){return{deleteApiTeacherApiCourse:async(e,a,r={})=>{P("deleteApiTeacherApiCourse","courseId",e);const h="/api/teacher-api/courses/{courseId}".replace("{courseId}",encodeURIComponent(String(e))),c=new URL(h,S);let o;s&&(o=s.baseOptions);const l={method:"DELETE",...o,...r},t={},p={};a!=null&&(t.Authorization=String(a)),T(c,p);let i=o&&o.headers?o.headers:{};return l.headers={...t,...i,...r.headers},{url:x(c),options:l}},getApiStudentApiCourseBills:async(e,a,r,h,c={})=>{const o="/api/student-api/course-bills",l=new URL(o,S);let t;s&&(t=s.baseOptions);const p={method:"GET",...t,...c},i={},n={};e!==void 0&&(n.page=e),a!==void 0&&(n.per_page=a),r!==void 0&&(n.pay_status=r),h!=null&&(i.Authorization=String(h)),T(l,n);let d=t&&t.headers?t.headers:{};return p.headers={...i,...d,...c.headers},{url:x(l),options:p}},getApiStudentApiCourses:async(e,a,r,h,c,o,l={})=>{const t="/api/student-api/courses",p=new URL(t,S);let i;s&&(i=s.baseOptions);const n={method:"GET",...i,...l},d={},A={};e!==void 0&&(A.page=e),a!==void 0&&(A.per_page=a),r!==void 0&&(A.course_name=r),h!==void 0&&(A.course_date=h),c!==void 0&&(A.bill_fees=c),o!=null&&(d.Authorization=String(o)),T(p,A);let y=i&&i.headers?i.headers:{};return n.headers={...d,...y,...l.headers},{url:x(p),options:n}},getApiTeacherApiCourse:async(e,a,r={})=>{P("getApiTeacherApiCourse","courseId",e);const h="/api/teacher-api/courses/{courseId}".replace("{courseId}",encodeURIComponent(String(e))),c=new URL(h,S);let o;s&&(o=s.baseOptions);const l={method:"GET",...o,...r},t={},p={};a!=null&&(t.Authorization=String(a)),T(c,p);let i=o&&o.headers?o.headers:{};return l.headers={...t,...i,...r.headers},{url:x(c),options:l}},getApiTeacherApiCourses:async(e,a,r,h,c,o,l={})=>{const t="/api/teacher-api/courses",p=new URL(t,S);let i;s&&(i=s.baseOptions);const n={method:"GET",...i,...l},d={},A={};e!==void 0&&(A.page=e),a!==void 0&&(A.per_page=a),r!==void 0&&(A.name=r),h!==void 0&&(A.date=h),c!==void 0&&(A.fees=c),o!=null&&(d.Authorization=String(o)),T(p,A);let y=i&&i.headers?i.headers:{};return n.headers={...d,...y,...l.headers},{url:x(p),options:n}},patchApiStudentApiCourseBillPay:async(e,a,r,h={})=>{P("patchApiStudentApiCourseBillPay","courseBillId",e);const c="/api/student-api/course-bills/{courseBillId}/pay".replace("{courseBillId}",encodeURIComponent(String(e))),o=new URL(c,S);let l;s&&(l=s.baseOptions);const t={method:"PATCH",...l,...h},p={},i={};a!=null&&(p.Authorization=String(a)),p["Content-Type"]="application/json",T(o,i);let n=l&&l.headers?l.headers:{};return t.headers={...p,...n,...h.headers},t.data=b(r,t,s),{url:x(o),options:t}},patchApiTeacherApiCourse:async(e,a,r,h={})=>{P("patchApiTeacherApiCourse","courseId",e);const c="/api/teacher-api/courses/{courseId}".replace("{courseId}",encodeURIComponent(String(e))),o=new URL(c,S);let l;s&&(l=s.baseOptions);const t={method:"PATCH",...l,...h},p={},i={};a!=null&&(p.Authorization=String(a)),p["Content-Type"]="application/json",T(o,i);let n=l&&l.headers?l.headers:{};return t.headers={...p,...n,...h.headers},t.data=b(r,t,s),{url:x(o),options:t}},postApiTeacherApiCourses:async(e,a,r={})=>{const h="/api/teacher-api/courses",c=new URL(h,S);let o;s&&(o=s.baseOptions);const l={method:"POST",...o,...r},t={},p={};e!=null&&(t.Authorization=String(e)),t["Content-Type"]="application/json",T(c,p);let i=o&&o.headers?o.headers:{};return l.headers={...t,...i,...r.headers},l.data=b(a,l,s),{url:x(c),options:l}}}},v=function(s){const e=R(s);return{async deleteApiTeacherApiCourse(a,r,h){var t,p;const c=await e.deleteApiTeacherApiCourse(a,r,h),o=(s==null?void 0:s.serverIndex)??0,l=(p=(t=u["CourseCourseApi.deleteApiTeacherApiCourse"])==null?void 0:t[o])==null?void 0:p.url;return(i,n)=>C(c,O,V,s)(i,l||n)},async getApiStudentApiCourseBills(a,r,h,c,o){var i,n;const l=await e.getApiStudentApiCourseBills(a,r,h,c,o),t=(s==null?void 0:s.serverIndex)??0,p=(n=(i=u["CourseCourseApi.getApiStudentApiCourseBills"])==null?void 0:i[t])==null?void 0:n.url;return(d,A)=>C(l,O,V,s)(d,p||A)},async getApiStudentApiCourses(a,r,h,c,o,l,t){var d,A;const p=await e.getApiStudentApiCourses(a,r,h,c,o,l,t),i=(s==null?void 0:s.serverIndex)??0,n=(A=(d=u["CourseCourseApi.getApiStudentApiCourses"])==null?void 0:d[i])==null?void 0:A.url;return(y,B)=>C(p,O,V,s)(y,n||B)},async getApiTeacherApiCourse(a,r,h){var t,p;const c=await e.getApiTeacherApiCourse(a,r,h),o=(s==null?void 0:s.serverIndex)??0,l=(p=(t=u["CourseCourseApi.getApiTeacherApiCourse"])==null?void 0:t[o])==null?void 0:p.url;return(i,n)=>C(c,O,V,s)(i,l||n)},async getApiTeacherApiCourses(a,r,h,c,o,l,t){var d,A;const p=await e.getApiTeacherApiCourses(a,r,h,c,o,l,t),i=(s==null?void 0:s.serverIndex)??0,n=(A=(d=u["CourseCourseApi.getApiTeacherApiCourses"])==null?void 0:d[i])==null?void 0:A.url;return(y,B)=>C(p,O,V,s)(y,n||B)},async patchApiStudentApiCourseBillPay(a,r,h,c){var p,i;const o=await e.patchApiStudentApiCourseBillPay(a,r,h,c),l=(s==null?void 0:s.serverIndex)??0,t=(i=(p=u["CourseCourseApi.patchApiStudentApiCourseBillPay"])==null?void 0:p[l])==null?void 0:i.url;return(n,d)=>C(o,O,V,s)(n,t||d)},async patchApiTeacherApiCourse(a,r,h,c){var p,i;const o=await e.patchApiTeacherApiCourse(a,r,h,c),l=(s==null?void 0:s.serverIndex)??0,t=(i=(p=u["CourseCourseApi.patchApiTeacherApiCourse"])==null?void 0:p[l])==null?void 0:i.url;return(n,d)=>C(o,O,V,s)(n,t||d)},async postApiTeacherApiCourses(a,r,h){var t,p;const c=await e.postApiTeacherApiCourses(a,r,h),o=(s==null?void 0:s.serverIndex)??0,l=(p=(t=u["CourseCourseApi.postApiTeacherApiCourses"])==null?void 0:t[o])==null?void 0:p.url;return(i,n)=>C(c,O,V,s)(i,l||n)}}};class U extends I{deleteApiTeacherApiCourse(e,a){return v(this.configuration).deleteApiTeacherApiCourse(e.courseId,e.authorization,a).then(r=>r(this.axios,this.basePath))}getApiStudentApiCourseBills(e={},a){return v(this.configuration).getApiStudentApiCourseBills(e.page,e.perPage,e.payStatus,e.authorization,a).then(r=>r(this.axios,this.basePath))}getApiStudentApiCourses(e={},a){return v(this.configuration).getApiStudentApiCourses(e.page,e.perPage,e.courseName,e.courseDate,e.billFees,e.authorization,a).then(r=>r(this.axios,this.basePath))}getApiTeacherApiCourse(e,a){return v(this.configuration).getApiTeacherApiCourse(e.courseId,e.authorization,a).then(r=>r(this.axios,this.basePath))}getApiTeacherApiCourses(e={},a){return v(this.configuration).getApiTeacherApiCourses(e.page,e.perPage,e.name,e.date,e.fees,e.authorization,a).then(r=>r(this.axios,this.basePath))}patchApiStudentApiCourseBillPay(e,a){return v(this.configuration).patchApiStudentApiCourseBillPay(e.courseBillId,e.authorization,e.patchApiStudentApiCourseBillPayRequest,a).then(r=>r(this.axios,this.basePath))}patchApiTeacherApiCourse(e,a){return v(this.configuration).patchApiTeacherApiCourse(e.courseId,e.authorization,e.patchApiTeacherApiCourseRequest,a).then(r=>r(this.axios,this.basePath))}postApiTeacherApiCourses(e={},a){return v(this.configuration).postApiTeacherApiCourses(e.authorization,e.patchApiTeacherApiCourseRequest,a).then(r=>r(this.axios,this.basePath))}}export{U as C};