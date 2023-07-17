/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';
import * as mdb from 'mdb-ui-kit';
import * as bootstrap from 'bootstrap';
import $ from 'jquery';

window.mdb = mdb;
window.bs = bootstrap;
global.$ = $

