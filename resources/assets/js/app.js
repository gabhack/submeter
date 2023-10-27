/**
 * First we will load all of this project's JavaScript dependencies which
 * includes React and other helpers. It's a great starting point while
 * building robust, powerful web applications using React + Laravel.
 */
import axios from 'axios';


axios.defaults.maxRedirects = 0;
axios.defaults.validateStatus = () => {
    return status <= 300;
};

require('./bootstrap');

/**
 * Next, we will create a fresh React component instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

require('./components/statistics/StatisticConfigList');
require('./components/statistics/StatisticConfig');
require('./components/statistics/StatisticConfigChart');
require('./components/manual/ManualData');


