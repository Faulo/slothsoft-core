pipeline {
	agent {
		docker {
			image 'faulo/farah:8.0'
			label 'docker'
		}
	}
	stages {
		stage('Install dependencies') {
			steps {
				callShell 'composer install'
			}
		}
		stage('Run Tests') {
			steps {
				callShell 'composer exec phpunit -- --log-junit report.xml'
			}
			post {
				always {
					junit 'report.xml'
				}
			}
		}
	}
}