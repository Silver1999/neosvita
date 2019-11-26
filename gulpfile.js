// ========================= gulp commands =========================
/*
smartgrid - генерирует новую сетку
watcher - запускает HTML-сервер
less - перевод в CSS без оптимизаций
js - без оптимизации
minjs - минифицировать
img - оптимизация изображений
build - оптимизация под прожакшн
*/
// ========================= Vars =========================
const gulp = require('gulp');
const less = require('gulp-less');
const smartgrid = require('smart-grid');
const rename = require("gulp-rename");
const autoprefixer = require('gulp-autoprefixer');
const cleanCSS = require('gulp-clean-css');
const gcmq = require('gulp-group-css-media-queries');

const uglify = require('gulp-uglify');
const concat = require('gulp-concat');

const imagemin = require('gulp-imagemin');
const imgCompress = require('imagemin-jpeg-recompress');

const browserSync = require('browser-sync').create();

// ========================= Smart-grid config =========================

const settings = {
	/* less || scss || sass || styl */
	outputStyle: 'less',
	/* number of grid columns */
	columns: 12,
	/* gutter width px || % || rem */
	offset: '20px',
	/* mobileFirst ? 'min-width' : 'max-width' */
	mobileFirst: false,
	container: {
		/* max-width оn very large screen */
		maxWidth: '940px',
		fields: '20px'
	},
	breakPoints: {
		md: {
			width: '900px', //tablets
			fields: '15px'
		},
		sm: {
			width: '680px', //phones landscape
			fields: '10px'
		},
		xs: {
			width: '576px', //old phones and other
			fields: '10px'
		}
		/*
		some_name: {
		    width: 'Npx',
		    fields: 'N(px|%|rem)',
		    offset: 'N(px|%|rem)'
		}
		*/
	}
};

gulp.task('smartgrid', function () {
	smartgrid('./resources/less', settings);
})

// ========================= Tasks =========================

gulp.task('less', function () {
	return gulp.src('./resources/less/index.less') // gulp.src(cssFileSequence)
		.pipe(less())
		.pipe(rename('index.min.css'))
		.pipe(gulp.dest('./public/css/'))
});

gulp.task('js', function () {
	return gulp.src('./resources/js/index.js')
		.pipe(rename('index.min.js'))
		.pipe(gulp.dest('./public/js/'));
});

gulp.task('less_admin', function () {
	return gulp.src('./resources/less/admin.less')
		.pipe(less())
		.pipe(rename('admin.min.css'))
		.pipe(gulp.dest('./public/css/'));
});

gulp.task('js_admin', function () {
	return gulp.src('./resources/js/admin.js')
		.pipe(rename('admin.min.js'))
		.pipe(gulp.dest('./public/js/'));
});

//----------------------------------------------------------------------
gulp.task('schedule_js', function () {
	return gulp.src('./resources/js/schedule.js')
		.pipe(rename('schedule.min.js'))
		.pipe(gulp.dest('./public/js/'));
});

function build_schedule() {
	return gulp.src('./resources/js/schedule.js')
		.pipe(uglify({
			toplevel: false
		}))
		.pipe(rename('schedule.min.js'))
		.pipe(gulp.dest('./public/js/'))
}

gulp.task('group_js', function () {
	return gulp.src('./resources/js/group.js')
		.pipe(rename('group.min.js'))
		.pipe(gulp.dest('./public/js/'));
});

function build_group() {
	return gulp.src('./resources/js/group.js')
		.pipe(uglify({
			toplevel: false
		}))
		.pipe(rename('group.min.js'))
		.pipe(gulp.dest('./public/js/'))
}

gulp.task('list_js', function () {
	return gulp.src('./resources/js/list.js')
		.pipe(rename('list.min.js'))
		.pipe(gulp.dest('./public/js/'));
});

function build_list() {
	return gulp.src('./resources/js/list.js')
		.pipe(uglify({
			toplevel: false
		}))
		.pipe(rename('list.min.js'))
		.pipe(gulp.dest('./public/js/'))
}
//----------------------------------------------------------------------

gulp.task('watcher', function () {
	browserSync.init({
		proxy: 'http://newedline.local/',
		online: false
	});

	gulp.watch([
		'./app/**/*',
		'./routes/**/*',
		'./resources/views/**/*',
	]).on('change', browserSync.reload);
	gulp.watch('./resources/less/**/*', gulp.series('less')).on('change', browserSync.reload);
	gulp.watch('./resources/js/index.js', gulp.series('js')).on('change', browserSync.reload);
	gulp.watch('./resources/js/schedule.js', gulp.series('schedule_js')).on('change', browserSync.reload);
	gulp.watch('./resources/js/group.js', gulp.series('group_js')).on('change', browserSync.reload);
	gulp.watch('./resources/js/list.js', gulp.series('list_js')).on('change', browserSync.reload);
	// gulp.watch('./resources/less/admin.less', gulp.series('less_admin')).on('change', browserSync.reload);
	// gulp.watch('./resources/js/admin.js', gulp.series('js_admin')).on('change', browserSync.reload);
	// gulp.watch(['./index.html', './html/*.html']).on('change', browserSync.reload);
});

// ========================= BUILD/RELEASE =========================

const build = gulp.series(build_JS, build_CSS, build_schedule, build_group, build_list);
exports.build = build;

function build_CSS() {
	return gulp.src('./resources/less/index.less')
		.pipe(less())
		.pipe(gcmq())
		.pipe(autoprefixer({
			cascade: true
		}))
		.pipe(cleanCSS({
			level: 2
		}))
		.pipe(rename('index.min.css'))
		.pipe(gulp.dest('./public/css/'));
}

function build_JS() {
	return gulp.src('./resources/js/index.js')
		.pipe(uglify({
			toplevel: false
		}))
		.pipe(rename('index.min.js'))
		.pipe(gulp.dest('./public/js/'))
}

gulp.task('minjs', function () {
	return gulp.src('./resources/js/summernote.js')
		.pipe(uglify({
			toplevel: true
		}))
		.pipe(rename('summernote.min.js'))
		.pipe(gulp.dest('./public/js/'))
});

gulp.task('img', function () {
	return gulp.src('./resources/img/**/*')
		.pipe(imagemin([
			imgCompress({
				loops: 4,
				min: 70,
				max: 80,
				quality: 'high'
			}),
			imagemin.gifsicle(),
			imagemin.optipng(),
			imagemin.svgo()
		]))
		.pipe(gulp.dest('./public/img/'));
});
