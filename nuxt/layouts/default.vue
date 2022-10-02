<template>
    <div class="default">
      <Menu class="z-10" />
      <Nuxt class="z-0" />
    </div>
</template>

<style>



.default {
  background: #eeeeee;
  min-height: 100vh;
  padding: 0 5% 0 5%;
  padding-bottom: 100px;
}
.flex {
  display: flex;
  justify-content: center;
  align-items: center;
}
.flex-wrap {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
}
.z-0 {
  z-index: 0;
}
.z-10 {
  z-index: 10;
  position: relative;
}

.page-enter-active {
  transition: opacity 1.3s;
}
.page-leave-active {
  transition: opacity 0.5s;
}
.page-enter,
.page-leave-active {
  opacity: 0;
}

*,
*::before,
*::after {
  box-sizing: border-box;
}

*::-webkit-scrollbar {
  display: none;
}

* {
  scrollbar-width: none;
}

*:disabled {
  opacity: 0.5;
}

input {
  font-size: inherit;
}




/* reset css */

html,
body,
div,
span,
object,
iframe,
h1,
h2,
h3,
h4,
h5,
h6,
p,
blockquote,
pre,
abbr,
address,
cite,
code,
del,
dfn,
em,
img,
ins,
kbd,
q,
samp,
small,
strong,
sub,
sup,
var,
b,
i,
dl,
dt,
dd,
ol,
ul,
li,
fieldset,
form,
label,
legend,
table,
caption,
tbody,
tfoot,
thead,
tr,
th,
td,
article,
aside,
canvas,
details,
figcaption,
figure,
footer,
header,
hgroup,
menu,
nav,
section,
summary,
time,
mark,
audio,
video {
  margin: 0;
  padding: 0;
  border: 0;
  outline: 0;
  font-size: 100%;
  vertical-align: baseline;
  background: transparent;
}

body {
  line-height: 1;
  font-family: YuGothic, "Yu Gothic Medium", "Yu Gothic", Meiryo, sans-serif;
}

* {
  line-height: 1;
  font-family: YuGothic, "Yu Gothic Medium", "Yu Gothic", Meiryo, sans-serif;
}

article,
aside,
details,
figcaption,
figure,
footer,
header,
hgroup,
menu,
nav,
section {
  display: block;
}

nav ul {
  list-style: none;
}

blockquote,
q {
  quotes: none;
}

blockquote:before,
blockquote:after,
q:before,
q:after {
  content: "";
  content: none;
}

a {
  margin: 0;
  padding: 0;
  font-size: 100%;
  vertical-align: baseline;
  background: transparent;
  text-decoration: none;
  color: inherit;
  cursor: pointer;
}

button {
  cursor: pointer;
}

/* change colours to suit your needs */
ins {
  background-color: #ff9;
  color: #000;
  text-decoration: none;
}

/* change colours to suit your needs */
mark {
  background-color: #ff9;
  color: #000;
  font-style: italic;
  font-weight: bold;
}

del {
  text-decoration: line-through;
}

abbr[title],
dfn[title] {
  border-bottom: 1px dotted;
  cursor: help;
}

table {
  border-collapse: collapse;
  border-spacing: 0;
}

/* change border colour to suit your needs */
hr {
  display: block;
  height: 1px;
  border: 0;
  border-top: 1px solid #cccccc;
  margin: 1em 0;
  padding: 0;
}

input,
select {
  vertical-align: middle;
}
</style>