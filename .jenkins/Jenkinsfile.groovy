def unstable(def body) {
	catchError(stageResult: 'UNSTABLE', buildResult: 'UNSTABLE', catchInterruptions: false) {
		body()
	}
}

def runComposerTest(def version, def variant) {
	unstable {
		callShell "composer update --prefer-${variant}"
		callShell "composer exec phpunit -- --log-junit .reports/${version}-${variant}.xml"
	}
}

def installFirefox() {
	unstable {
		if (isUnix()) {
			// already part of the farah image
		} else {
			callShell "choco install Firefox --no-progress --yes --skip-checksums --params='/NoTaskbarShortcut /NoDesktopShortcut /NoStartMenuShortcut /NoAutoUpdate'"
		}
	}
}

pipeline {
	agent none
	options {
		disableConcurrentBuilds()
		disableResume()
		disableRestartFromStage()
		skipDefaultCheckout()
	}
	environment {
		COMPOSER_PROCESS_TIMEOUT = '3600'
	}
	stages {
		stage('Setup') {
			steps {
				script {
					def config = readProperties file: '.jenkins/phpProject.properties'

					def platforms = config.PLATFORMS.split(' ')
					def versions = config.PHP_VERSIONS.split(' ')
					def variants = config.COMPOSER_VARIANTS.split(' ')

					def branches = [:]

					for (def p in platforms) {
						def platform = p
						for (def v in versions) {
							def version = v
							for (def a in variants) {
								def variant = a

								def name = "${platform} php-${version} ${variant}"
								def label = "${platform} && docker"
								def workspace = "php-${version}-${variant}"

								branches[name] = {
									stage(name) {
										node(label) {
											ws("${WORKSPACE}/${workspace}") {
												checkout scm

												dir('.reports') {
													deleteDir()
												}

												docker.image("faulo/farah:${version}").inside {
													if (config.FARAH_INSTALL_FIREFOX == '1') {
														installFirefox()
													}

													runComposerTest(version, variant)
												}

												dir('.reports') {
													junit "*.xml"
												}
											}
										}
									}
								}
							}
						}
					}

					parallel branches
				}
			}
		}
	}
}