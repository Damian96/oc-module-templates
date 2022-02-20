const { src, dest, series, parallel } = require('gulp');

const gulp = require('gulp');
const rename = require('gulp-rename');
const path = require('path')

const paths = {
    project: 'opencart-sample',
    target: '../opencart-sample/htdocs/'
};

function copy() {
    return src([
        './module_templates.ocmod/upload/**/*.php',
        './module_templates.ocmod/upload/**/*.twig'
        ])
        .pipe(rename(function(path) {
            return {
                dirname: path.dirname,
                basename: path.basename,
                extname: path.extname
            };
        }))
        .pipe(dest([paths.target]));
}

function watch() {
	gulp.watch('./module_templates.ocmod/upload/**/*.php', copy);
	gulp.watch('./module_templates.ocmod/upload/**/*.twig', copy);
}

// define tasks
const build = parallel(copy);

// export tasks
exports.watch = watch;
exports.default = build;
