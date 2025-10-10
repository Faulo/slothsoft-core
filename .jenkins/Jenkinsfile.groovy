def runTests(def versions) {
	for (version in versions) {
		def image = "faulo/farah:${version}"

		stage("PHP: ${version}") {
			callShell "docker pull ${image}"

			withDockerContainer(image: image, toolName: 'Default') {
				callShell 'composer update --prefer-lowest'

				dir('.reports') {
					deleteDir()
				}

				def report = ".reports/${version}.xml"

				catchError(stageResult: 'UNSTABLE', buildResult: 'UNSTABLE', catchInterruptions: false) {
					callShell "composer exec phpunit -- --log-junit ${report}"
				}

				junit report
			}
		}
	}
}

pipeline {
	agent none
	options {
		disableConcurrentBuilds()
		disableResume()
	}
	environment {
		COMPOSER_PROCESS_TIMEOUT = '3600'
	}
	stages {
		stage('Linux') {
			agent {
				label 'docker && linux'
			}
			steps {
				script {
					runTests(["7.4", "8.0", "8.1", "8.2", "8.3"])
				}
			}
		}
		stage('Windows') {
			agent {
				label 'docker && windows'
			}
			steps {
				script {
					runTests(["7.4", "8.0", "8.1", "8.2", "8.3"])
				}
			}
		}
	}
}