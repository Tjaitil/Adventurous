<template>
  <header>
    <div id="nav">
      <div class="top_bar">
        <div class="top_a"><a href="/main" aria-label="Main"></a></div>
        <div class="top_but"><a href="/main">Main</a></div>
      </div>
      <div class="top_bar">
        <div class="top_a"><a href="/advclient" aria-label="Client"></a></div>
        <div class="top_but"><a href="advclient">Client</a></div>
      </div>
      <div class="top_bar">
        <div class="top_a">
          <a href="/gameguide" aria-label="Gameguide"></a>
        </div>
        <div class="top_but"><a href="/gameguide">Gameguide</a></div>
      </div>
      <div class="top_bar">
        <div class="top_a">
          <a href="/highscores" aria-label="Highscores"></a>
        </div>
        <div class="top_but"><a href="/highscores">Highscores</a></div>
      </div>
      <div class="top_bar">
        <div class="top_a"><a href="/news" aria-label="News"></a></div>
        <div class="top_but"><a href="/news">News</a></div>
      </div>
      <div class="top_bar">
        <div class="top_a"><a href="/profile" aria-label="Profile"></a></div>
        <div class="top_but"><a href="/profile">Profile</a></div>
      </div>
      <div class="top_bar">
        <div class="top_bar">
          <div class="top_a"><a href="/logout" aria-label="Logout"></a></div>
          <div class="top_but"><a href="/logout">Logout</a></div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts"></script>

<style>
header {
  /*grid-columns: 1 / 3;*/
  border-bottom: black thin;
  text-align: center;
  display: block;
  background: radial-gradient(
    var(--layoutBgColor),
    var(--layoutBgColorShadowPerc),
    var(--layoutBgColorShadow)
  );
  display: flex;
  justify-content: center;
  align-items: center;
}
#nav_but {
  display: none;
  color: white;
  background-color: #994d00;
  width: 100%;
  max-width: 100px;
  border: none;
}
#nav_2 {
  margin-bottom: 10px;
}
#nav_2 a {
  background-color: #f2e6d9;
  border-radius: 5px;
  border: 1px outset;
  box-shadow: 1px 1px 1px 1px #372715;
  padding: 10px 12px 10px 12px;
  color: black;
  font-weight: bold;
  cursor: pointer;
  text-decoration: none;
}
.top_bar {
  min-width: 170px;
  margin: 0px 0px 10px 0px;
  position: relative;
  display: inline-block;
  transition: var(--filterAnimation);
}
.top_bar:hover {
  filter: brightness(var(--transitionDuration));
}
.top_a {
  display: block;
  height: 75px;
  width: 75px;
  color: white;
  background-color: #994d00;
  margin: 0 auto;
  border-radius: 40px;
  z-index: 2;
  position: relative;
  top: 10px;
  left: 0px;
  text-decoration: none;
  box-shadow: 0px 0px 20px black;
  line-height: 50px;
}
.top_a a {
  text-decoration: none;
  color: white;
  display: block;
  line-height: 70px;
}
.top_but {
  max-width: 130px;
  height: 25px;
  background-color: #994d00;
  position: relative;
  z-index: 2;
  margin: 0 auto;
  bottom: 40px;
  text-align: center;
  border-radius: 2px;
  box-shadow: 0px 0px 5px black;
}
.top_but a {
  text-align: center;
  text-decoration: none;
  line-height: 25px;
  color: #ffffff;
}

@media all and (max-width: 830px) {
  #nav_but {
    display: block;
  }
  .top_bar {
    font-size: 8px;
  }
  #nav_2 {
    font-size: 13px;
  }
}
@media all and (max-width: 1040px) {
  #nav_but {
    display: block;
  }
}
@media all and (max-width: 1240px) {
  .top_bar {
    margin: 5px 0px 0px 0px;
  }
  .top_a {
    display: none;
  }
  .top_but {
    bottom: 0px;
    width: 100%;
    left: 0px;
  }
  .top_but a {
    bottom: 0px;
    margin: 0px;
  }
}
</style>
