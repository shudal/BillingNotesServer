<?php

Route::rule('/origin', 'index/server/index');

Route::post('/login', 'index/login/index');
Route::post("/add", "index/index/add");

Route::get("/bills", "index/index/all");

Route::get("/usermeta", "index/UserMeta/index");
Route::post("/usermeta/changeNick", "index/UserMeta/changeNick");
Route::post("/usermeta/changeSd", "index/UserMeta/changeSd");
Route::post("/usermeta/changeMf", "index/UserMeta/changeMf");
Route::post("/feedback/add", "index/FeedBack/add");

Route::rule("/user_state", "index/index/userState");

Route::post("/regis", "index/regis/add");
Route::get("/regis_check", "index/regis/check");

Route::post('/logout', "index/index/logout");

Route::get("/isPrompt", "index/Version/isPrompt");
Route::get("/prompt", "index/Version/prompt");
Route::get("/serverNormal", "index/Version/serverNormal");


// 笔记
Route::post("/note/add", "note/index/add");
Route::get("/notes", "note/index/all");
