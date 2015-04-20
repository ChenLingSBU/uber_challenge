/**
 * main js file. This file is used for Require JS to configure and load initially dependencies.
 *  
 * @author Chen Ling <chling.sbu@gmail.com>
 * @copyright Chen Ling 2015
 * Released under the MIT License
 *
 */

require.config({
  paths: {
    jquery: '../lib/jquery-1.11.2.min',
    underscore: '../lib/underscore-min',
    backbone: '../lib/backbone-min',
    next_departure_model: './next_departure_model'
  }
});

require([
'./next_departure_view',
], function() {
});


