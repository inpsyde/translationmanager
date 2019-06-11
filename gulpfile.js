const gulp = require('gulp')
const gulpPhpUnit = require('gulp-phpunit')
const gulpPhpcs = require('gulp-phpcs')
const gulpZip = require('gulp-zip')
const gulpDel = require('del')
const minimist = require('minimist')
const fs = require('fs')
const pump = require('pump')
const usage = require('gulp-help-doc')
const { exec } = require('child_process')
const semver = require('semver')

const PACKAGE_NAME = 'translationmanager'
const PACKAGE_DESTINATION = './dist'
const PACKAGE_PATH = `${PACKAGE_DESTINATION}/${PACKAGE_NAME}`

const options = minimist(process.argv.slice(2), {
  string: [
    'packageVersion',
    'compressPath',
    'compressedName',
  ],
  default: {
    compressPath: process.compressPath || '.',
    packageVersion: process.packageVersion || '',
    compressedName: process.compressedName || '',
  },
})

/**
 * Check the Package Version value is passed to the script
 * @param done
 * @throws Error if the package version option isn't found
 */
async function validatePackageVersion (done)
{
  await 1

  if (!'packageVersion' in options || '' === options.packageVersion) {
    done()
  }

  if (semver.valid(options.packageVersion) === null) {
    throw new Error(
      'Invalid package version, please follow MAJOR.MINOR.PATCH semver convention.',
    )
  }

  done()
}

/**
 * Run composer for dist
 * @param done
 * @returns {Promise}
 */
function composer (done)
{
  return exec(
    `composer install --prefer-dist --optimize-autoloader --no-dev --working-dir=${PACKAGE_PATH}`,
  )
}

/**
 * PHP Unit Task
 * @returns {*}
 */
function phpunit (done)
{
  return new Promise(() => {
    pump(
      gulp.src('./phpunit.xml.dist'),
      gulpPhpUnit(
        './vendor/bin/phpunit',
        {
          debug: false,
          clear: false,
          notify: false,
          statusLine: false,
        },
      ),
      done,
    )
  })
}

/**
 * PHPCS Task
 * @returns {*}
 */
function phpcs ()
{
  return gulp.src('./src/**/*.php').pipe(gulpPhpcs({
    bin: './vendor/bin/phpcs',
    standard: 'Inpsyde',
  })).pipe(
    gulpPhpcs.reporter('fail', { failOnFirst: true }),
  )
}

/**
 * Create the package
 * @returns {*}
 */
function copyPackageFiles (done)
{
  return new Promise(() => {
    pump(
      gulp.src([
        './assets/**/*',
        './inc/**/*',
        './resources/js/**/*',
        './resources/img/**/*',
        './src/**/*',
        './views/**/*',
        'LICENSE',
        'readme.txt',
        'translationmanager.php',
        './composer.json',
        './composer.lock',
      ], {
        base: './',
      }),
      gulp.dest(PACKAGE_PATH),
      done,
    )
  })
}

/**
 * Clean Up Dist
 * @returns {Promise<void>}
 */
async function cleanUpDist()
{
  return await gulpDel([
    `${PACKAGE_DESTINATION}/**/changelog.txt`,
    `${PACKAGE_DESTINATION}/**/changelog.md`,
    `${PACKAGE_DESTINATION}/**/CHANGELOG.md`,
    `${PACKAGE_DESTINATION}/**/CHANGELOG`,
    `${PACKAGE_DESTINATION}/**/README`,
    `!${PACKAGE_PATH}/README`,
    `${PACKAGE_DESTINATION}/**/LICENSE`,
    `!${PACKAGE_PATH}/LICENSE`,
    `${PACKAGE_DESTINATION}/**/README.md`,
    `!${PACKAGE_PATH}/README.md`,
    `${PACKAGE_DESTINATION}/**/readme.md`,
    `!${PACKAGE_PATH}/readme.md`,
    `${PACKAGE_DESTINATION}/**/readme.txt`,
    `!${PACKAGE_PATH}/readme.txt`,
    `${PACKAGE_DESTINATION}/**/README.rst`,
    `${PACKAGE_DESTINATION}/**/CONTRIBUTING.md`,
    `${PACKAGE_DESTINATION}/**/CONTRIBUTING`,
    `${PACKAGE_DESTINATION}/**/composer.json`,
    `${PACKAGE_DESTINATION}/**/composer.lock`,
    `${PACKAGE_DESTINATION}/**/phpcs.xml`,
    `${PACKAGE_DESTINATION}/**/phpcs.xml.dist`,
    `${PACKAGE_DESTINATION}/**/phpunit.xml`,
    `${PACKAGE_DESTINATION}/**/phpunit.xml.dist`,
    `${PACKAGE_DESTINATION}/**/.gitignore`,
    `${PACKAGE_DESTINATION}/**/.travis.yml`,
    `${PACKAGE_DESTINATION}/**/.scrutinizer.yml`,
    `${PACKAGE_DESTINATION}/**/.gitattributes`,
    `${PACKAGE_DESTINATION}/**/bitbucket-pipelines.yml`,
    `${PACKAGE_DESTINATION}/**/test`,
    `${PACKAGE_DESTINATION}/**/tests`,
    `${PACKAGE_DESTINATION}/**/bin`,
  ])
}

/**
 * Compress the package
 * @returns {*}
 */
function compressPackage (done)
{
  const { packageVersion, compressPath } = options
  const timeStamp = new Date().getTime()

  if (!fs.existsSync(PACKAGE_DESTINATION)) {
    throw new Error(
      `Cannot create package, ${PACKAGE_DESTINATION} doesn't exists.`)
  }

  return new Promise(() => {
    exec(
      `git log -n 1 | head -n 1 | sed -e 's/^commit //' | head -c 8`,
      {},
      (error, stdout) => {
        let shortHash = error ? timeStamp : stdout
        const compressedName = options.compressedName ||
          `${PACKAGE_NAME}-${packageVersion}-${shortHash}`

        pump(
          gulp.src(`${PACKAGE_DESTINATION}/**/*`, {
            base: PACKAGE_DESTINATION,
          }),
          gulpZip(`${compressedName}.zip`),
          gulp.dest(
            compressPath,
            {
              base: PACKAGE_DESTINATION,
              cwd: './',
            },
          ),
          done,
        )
      },
    )
  })
}

/**
 * Delete content within the Dist directory
 * @returns {*}
 */
async function cleanDist ()
{
  return await gulpDel(PACKAGE_DESTINATION)
}

/**
 * Gulp Help
 * @returns {Promise}
 */
function help ()
{
  return usage(gulp)
}

/**
 * Run Tests
 *
 * @task {tests}
 */
exports.tests = gulp.series(
  phpcs,
  phpunit,
)

/**
 * Create the plugin package distribution.
 *
 * @task {dist}
 * @arg {packageVersion} Package version, the version must to be conformed to semver.
 * @arg {compressPath} Where the resulting package zip have to be stored.
 * @arg {compressedName} The name to give to the package instead of the default one.
 */
exports.dist = gulp.series(
  validatePackageVersion,
  cleanDist,
  copyPackageFiles,
  composer,
  cleanUpDist,
  compressPackage,
  cleanDist,
)

exports.help = help
exports.default = help
