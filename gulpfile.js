// ## Globals
var argv         = require('minimist')(process.argv.slice(2));
var autoprefixer = require('gulp-autoprefixer');
var gulp         = require('gulp');
var gulpif       = require('gulp-if');
var cssNano      = require('gulp-cssnano');
var plumber      = require('gulp-plumber');
var rename       = require('gulp-rename');
var sass         = require('gulp-sass');
var uglify       = require('gulp-uglify');
var browserSync  = require('browser-sync').create();
var colors       = require('colors/safe');
var sftp         = require('gulp-sftp');
var debug        = require('gulp-debug');
var packageJson  = require('./package');
var concat       = require('gulp-concat');
var log          = require('fancy-log');
var fs           = require('fs');
var config = {};
if(fs.existsSync('./config.json')) {
	config       = require('./config.json');
}


// Error checking; produce an error rather than crashing.
var onError = function(err) {
	console.log(err.toString());
	this.emit('end');
};

var print = function(text, color) {
	color = color || 'yellow';
	console.log(colors[color].bold(text));
}


print('----------------------------------------------------------------');
print('Sage ' + packageJson.version + ' running');
print('Available tasks:');
print('1: gulp');
print('2: gulp watch [Uses browsersync to reload. Reloads streaming css and reloads full page for js]');
print('3: gulp ftp [Uploads frontend files to a external server');
print('');
print('Available params:');
print('1: --production [Minifies js and css]')
print('2: --all [Use with gulp ftp. Uploads all files on the server instead of just the dist folder]')

var opts = {
	production : argv.production || false,
	all        : argv.all        || false
};


/**
 * Works like this:
 * files[array] => Processing => Concats => output
 */
var manifest = {
	js : [
		{
			output : 'main.js',
			files  : [
				//'node_modules/photoswipe/dist/photoswipe.min.js',
				'assets/scripts/main.js'
			]
		}
	],
	css : [
		{
			output : 'main.css',
			files  : [
				'assets/styles/main.scss'
			]
		}
	],

  config : {
    devUrl : "http://localhost/testbed"
  },
  dist : "dist/"
}

gulp.task('styles', function() {
	manifest.css.map(function(css) {
		gulp.src(css.files)
			.pipe(plumber())
			.pipe(debug({title:'CSS:'}))
			.pipe(sass().on('error', sass.logError))
			.pipe(autoprefixer({
				browsers: [
					'last 2 versions',
					'android 4',
					'opera 12'
				]
			}))
			.pipe(gulpif(
				opts.production,
				cssNano()
			))
			.pipe(gulp.dest(manifest.dist + 'styles'))
			.pipe(browserSync.reload({stream:true}))
	});
});

gulp.task('scripts', function() {
	manifest.js.map(function(js) {
		gulp.src(js.files)
			.pipe(plumber())
			.pipe(debug({title:'JS:'}))
			.pipe(concat(js.output))
			.pipe(gulpif(
				opts.production,
				uglify()
			))
			.pipe(gulp.dest(manifest.dist + 'scripts'))
			.pipe(browserSync.reload({stream:true}))
	});
});

gulp.task('images', function() {
	gulp.src('assets/images/**/*')
		.pipe(plumber())
		.pipe(gulp.dest(manifest.dist + 'images'))
		.pipe(browserSync.reload({stream:true}))
});

gulp.task('fonts', function() {
	gulp.src('assets/fonts/**/*')
		.pipe(plumber())
		.pipe(gulp.dest(manifest.dist + 'fonts'))
		.pipe(browserSync.reload({stream:false}))
});

gulp.task('ftp', function() {

	// Change this to your wp themes folder
	var base = '/srv/users/wordpress';
	var conn = sftp({
		host       : config.host,
		user       : config.user,
		pass       : config.pass,
		port       : 22,
		remotePath : base
	});
	if(opts.all) {
		// This should work well out of the box but feel free to modify this list
		var globs = [
			'author.php',
			'404.php',
			'base.php',
			'category.php',
			'functions.php',
			'index.php',
			'page.php',
			'screenshot.png',
			'search.php',
			'single.php',
			'style.css',
			'acf-json/**',
			'vendor/**',
			'dist/**',
			'lib/**',
			'template*',
			'templates/**'
		];
	} else {
		var globs = [
			'dist/**'
		]
	}
	// using base = '.' will transfer everything to /public_html correctly
	// turn off buffering in gulp.src for best performance

	return gulp.src( globs, { base: '.', buffer: false } )
		.pipe(conn)
});



gulp.task('watch', function() {
	browserSync.init({
		files: ['{lib,templates}/**/*.php', '*.php'],
		proxy: manifest.config.devUrl,
		snippetOptions: {
			whitelist: ['/wp-admin/admin-ajax.php'],
			blacklist: ['/wp-admin/**']
		}
	});
	gulp.watch(['assets/' + 'styles/**/*'],  ['styles']);
	gulp.watch(['assets/' + 'scripts/**/*'], ['scripts']);
	gulp.watch(['assets/' + 'fonts/**/*'],   ['fonts']);
	gulp.watch(['assets/' + 'images/**/*'],  ['images']);
});

// ### Gulp
// `gulp` - Run a complete build. To compile for production run `gulp --production`.
gulp.task('default', function() {
	console.log(colors.yellow.bold('Running Default task'));
	gulp.start('styles');
	gulp.start('scripts');
	gulp.start('images');
	gulp.start('fonts');
	console.log(colors.yellow.bold('--------------------------------'));

	
});