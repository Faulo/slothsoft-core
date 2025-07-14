pipeline {
	agent {
		label 'docker'
	}
	options {
		disableConcurrentBuilds()
		disableResume()
	}
	stages {
		stage('Init') {
			steps {
				script {
					def versions = [
						"7.4",
						"8.0",
						"8.1",
						"8.2",
						"8.3"
					];

					for (version in versions) {
						def image = "faulo/farah:${version}"

						stage("PHP: ${version}") {
							callShell "docker pull ${image}"

							docker.image(image).inside {
								stage('Install dependencies') {
									callShell 'composer install'
								}
								stage('Run Tests') {
									catchError(stageResult: 'UNSTABLE', buildResult: 'UNSTABLE') {
										callShell 'composer exec phpunit -- --log-junit report.xml'
									}
									if (fileExists('report.xml')) {
										junit 'report.xml'
									}
								}
							}
						}
					}
				}
			}
		}
	}
}