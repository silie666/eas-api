import{u as a,_ as i}from"./hooks-2d8c7479.js";import{ar as e,d as c,c as p,e as u,f as s,o as m}from"./index-c5917c64.js";import{C as l}from"./course-course-api-5c1bca69.js";import"./hooks-f415898c.js";import"./search-cc37b371.js";import"./baseData-44696a71.js";function d(){const o=a(new l().getApiStudentApiCourses);return{columns:[{label:e("序号"),type:"index",width:70},{label:e("课程名称"),prop:"course.name",minWidth:130,search:{type:"input"}},{label:e("课程内容"),prop:"course.content",minWidth:130,search:{type:"input"}},{label:e("上课日期"),prop:"course.date",minWidth:130,search:{type:"dateRange"}}],hook:o}}const h={class:"main"},A=c({name:"CourseList",__name:"index",setup(o){const{columns:t,hook:n}=d();return(r,_)=>(m(),p("div",h,[u(i,{title:r.$t("我的课程"),columns:s(t),hook:s(n)},null,8,["title","columns","hook"])]))}});export{A as default};
