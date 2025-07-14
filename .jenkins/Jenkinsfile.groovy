pipeline {
	agent {
		docker {
			image 'farah:8.0'
			label 'docker'
		}
	}
	stages {
		stage('Run Tests') {
			steps {
				callShell 'vendor/bin/phpunit --log-junit junit-report.xml'
			}
		}
	}
	post {
		always {
			junit 'junit-report.xml'
		}
	}
}