import gulp from "gulp";
import yargs from "yargs";
import sass from "gulp-sass";
import cleanCSS from "gulp-clean-css";
import gulpIf from "gulp-if";
import sourcemaps from "gulp-sourcemaps";
import imagemin from "gulp-imagemin";
import del from "del";
import webpack from "webpack-stream";
import uglify from "gulp-uglify";
import named from "vinyl-named";
import zip from "gulp-zip";
import replace from "gulp-replace";
import info from "./package.json";
import rename from 'gulp-rename';
var rename_regex = require('gulp-regex-rename');



const PRODUCTION = yargs.argv.prod;

const paths = {
    styles: {
        src: ['src/assets/scss/admin.scss', 'src/assets/scss/public.scss'],
        dest: 'dist/assets/css'
    },
    images: {
        src:  'src/assets/images/**/*.{jpg,jpeg,png,svg,gif}',
        dest: 'dist/assets/images'
    },
    scripts: {
        src: ['src/assets/js/admin.js', 'src/assets/js/public.js'],
        dest: 'dist/assets/js'
    },
    others: {
        src: [ 
            'src/assets/**/*', '!src/assets/{scss,js,images}', '!src/assets/{scss,js,images}/**/*'],
        dest: 'dist/assets/'
    },
    loaders: {
        src: ''
    },
    package: {
        src: [ 
            '**/*', 
            '!.vscode', 
            '!node_modules{,/**}', 
            '!packaged{,/**}', 
            '!src{,/**}',
            '!.babelrc',
            '!.gitignore',
            '!gulpfile.babel.js',
            '!package-lock.json',
            '!package.json'
        ],
        dest: 'packaged'
    }
};


export const clean = (cb) => {
    return del(['dist']);
}

export const styles = (cb) => {
    return gulp.src(paths.styles.src)
        .pipe(gulpIf(!PRODUCTION, sourcemaps.init()))
        .pipe(sass().on('error', sass.logError))
        .pipe(gulpIf(PRODUCTION, cleanCSS({compatibility: 'ie8'})))
        .pipe(gulpIf(!PRODUCTION, sourcemaps.write()))
        .pipe(gulp.dest(paths.styles.dest));
}

export const images = (cb) => {
    return gulp.src(paths.images.src)
        .pipe(gulpIf(PRODUCTION, imagemin()))
        .pipe(gulp.dest(paths.images.dest));
}

export const scripts = (cb) => {
    return gulp.src(paths.scripts.src)
    .pipe(named())
    .pipe(webpack({
        module: {
            rules: [
                { test: /\.js$/, loader: 'babel-loader', options: {presets: ['@babel/preset-env']} },
            ],
        },
        mode: !PRODUCTION ? 'development' : 'production',
        output: {
            filename: '[name].js',
        },
        externals: {
            jquery: 'jQuery'
        },
        devtool: !PRODUCTION ? 'inline-source-map': false
    }))
    .pipe(gulpIf(PRODUCTION, uglify()))
    .pipe(gulp.dest(paths.scripts.dest));
}

export const copy = (cb) => {
    return gulp.src(paths.others.src)
        .pipe(gulp.dest(paths.others.dest));
}



export const watch = (cb) => {
    gulp.watch('src/assets/scss/**/*.scss', styles);
    gulp.watch('src/assets/js/**/*.js', scripts);
    gulp.watch(paths.images.src, images);
    gulp.watch(paths.others.src, copy);
}




export const dev = gulp.series(clean, gulp.parallel(styles, scripts, images, copy), watch);

export const compress = (cb) => {
    return gulp.src(paths.package.src, {base: '../'})
    //.pipe(replace('-pluginname', `-${info.name}`))
    //.pipe(replace('_pluginname', info.name))
    .pipe(gulpIf((file) => (file.relative.split('.').pop() !== 'zip'), replace('_themename', info.theme)))
    .pipe(rename_regex(/_themename_pluginname/, `${info.theme}${info.name}`))
    /*.pipe(
        rename(
          function(path) {
            path.dirname = `${info.theme}-${info.name}/`+ path.dirname;
          }
        )
    )*/
    .pipe(zip(`${info.theme}-${info.name}.zip`))
    .pipe(gulp.dest(paths.package.dest));
}





export const build = gulp.series(clean, gulp.parallel(styles, scripts, images, copy));

export const bundle = gulp.series(build, compress);

export default dev;