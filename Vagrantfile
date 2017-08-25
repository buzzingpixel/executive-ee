# ---------------------------------------------------------------------------
# Copyright 2017, BuzzingPixel, LLC
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the Apache License 2.0.
# http://www.apache.org/licenses/LICENSE-2.0
# ---------------------------------------------------------------------------

# Require json
require 'json'

# Specify Vagrant API version
VAGRANTFILE_API_VERSION ||= '2'

# Specify script paths
configFile = 'vagrantConfig/config.json'
configLocalFile = 'vagrantConfig/configLocal.json'
aliasesPath = 'vagrantConfig/aliases'
provisionPath = 'vagrantConfig/provision.sh'
startPath = 'vagrantConfig/start.sh'
customMessagePath = 'vagrantConfig/.custom_message'

# Configure
Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
    # Specify the vagrant box to use
    config.vm.box = 'buzzingpixel/buzzingpixel-lemp'

    # Load settings from config file
    jsonConfig = JSON.parse(File.read(configFile))

    # Check if there is a local config and merge it in
    if File.exist? configLocalFile then
        localConfig = JSON.parse(File.read(configLocalFile))
        jsonConfig = jsonConfig.merge(localConfig)
    end

    # Set the host name
    if jsonConfig.has_key? 'hostname' then
        config.vm.hostname = jsonConfig['hostname']
    end

    # Check if aliases file exists
    if File.exist? aliasesPath then
        config.vm.provision 'file', source: aliasesPath, destination: '~/.bash_aliases'
    end

    # Create a private network
    config.vm.network 'private_network', ip: jsonConfig['ip']

    # Set up sync directories
    if jsonConfig.has_key? 'directories' then
        jsonConfig['directories'].each { |d|
            if d.has_key? 'relative' and d['relative'] == true
                config.vm.synced_folder __dir__ + d['map'], d['to'], type: "nfs"
            else
                config.vm.synced_folder d['map'], d['to'], type: "nfs"
            end
        }
    end

    # Make sure key files and dirs exist and have appropriate permissions
     config.vm.provision 'shell' do |s|
        s.inline = <<-SHELL
            mkdir -p /home/vagrant/.ssh

            touch /home/vagrant/.ssh/authorized_keys
            chown vagrant:vagrant /home/vagrant/.ssh/authorized_keys
            chmod 0600 /home/vagrant/.ssh/authorized_keys

            touch /home/vagrant/.ssh/id_rsa.pub
            chown vagrant:vagrant /home/vagrant/.ssh/id_rsa.pub
            chmod 0644 /home/vagrant/.ssh/id_rsa.pub

            touch /home/vagrant/.ssh/id_rsa
            chown vagrant:vagrant /home/vagrant/.ssh/id_rsa
            chmod 0600 /home/vagrant/.ssh/id_rsa


            mkdir -p /root/.ssh

            touch /root/.ssh/authorized_keys
            chown vagrant:vagrant /root/.ssh/authorized_keys
            chmod 0600 /root/.ssh/authorized_keys

            touch /root/.ssh/id_rsa.pub
            chown vagrant:vagrant /root/.ssh/id_rsa.pub
            chmod 0644 /root/.ssh/id_rsa.pub

            touch /root/.ssh/id_rsa
            chown vagrant:vagrant /root/.ssh/id_rsa
            chmod 0600 /root/.ssh/id_rsa
        SHELL
    end

    # Check for public key
    if jsonConfig.has_key? 'publicKey' then
        # Replace the home indicator
        publicKey = jsonConfig['publicKey'].sub! '~', "#{Dir.home}"

        # Add the public key
        config.vm.provision "file", source: publicKey, destination: "/home/vagrant/.ssh/id_rsa.pub"

        # Run public key operations
        config.vm.provision 'shell' do |s|
            ssh_pub_key = File.readlines(publicKey).first.strip
            s.inline = <<-SHELL
                echo #{ssh_pub_key} >> /home/vagrant/.ssh/authorized_keys
                echo #{ssh_pub_key} >> /root/.ssh/authorized_keys

                chown vagrant:vagrant /home/vagrant/.ssh/id_rsa.pub
                chmod 0644 /home/vagrant/.ssh/id_rsa.pub

                cp /home/vagrant/.ssh/id_rsa.pub /root/.ssh/id_rsa.pub
                chown root:root /root/.ssh/id_rsa.pub
                chmod 0644 /root/.ssh/id_rsa.pub
            SHELL
        end
    end

    # Check for private key
    if jsonConfig.has_key? 'privateKey' then
        # Replace the home indicator
        privateKey = jsonConfig['privateKey'].sub! '~', "#{Dir.home}"

        # Add the private key
        config.vm.provision "file", source: privateKey, destination: "/home/vagrant/.ssh/id_rsa"

        # Run public key operations
        config.vm.provision 'shell' do |s|
            s.inline = <<-SHELL
                chown vagrant:vagrant /home/vagrant/.ssh/id_rsa
                chmod 0600 /home/vagrant/.ssh/id_rsa

                cp /home/vagrant/.ssh/id_rsa /root/.ssh/id_rsa
                chown root:root /root/.ssh/id_rsa
                chmod 0600 /root/.ssh/id_rsa
            SHELL
        end
    end

    # Check for custom message
    if File.exist? customMessagePath then
        config.vm.provision "shell" do |s|
            s.inline = <<-SHELL
                sed -i -e "s,. ~/.custom_message,,g" /home/vagrant/.bashrc
                # echo ". ~/.custom_message" >> /home/vagrant/.bashrc
                # echo ". ~/.custom_message" >> /home/vagrant/.bash_profile
                cp /vagrant/#{customMessagePath} /home/vagrant/.custom_message
            SHELL
        end
    end

    # Run virtualbox config
    config.vm.provider :virtualbox do |v|
        # Set the timesync threshold to 5 seconds instead of the default 20 minutes and set timesync to run automatically upon wake
        v.customize ["guestproperty", "set", :id, "/VirtualBox/GuestAdd/VBoxService/--timesync-set-threshold", "5000"]
        v.customize ["guestproperty", "set", :id, "/VirtualBox/GuestAdd/VBoxService/--timesync-set-start"]
        v.customize ["guestproperty", "set", :id, "/VirtualBox/GuestAdd/VBoxService/--timesync-set-on-restore", "1"]

        # Set the machine name
        if jsonConfig.has_key? 'name' then
            v.name = jsonConfig['name']
        end
    end

    # Run shell script provisioning on first box boot
    if File.exist? provisionPath then
        config.vm.provision :shell, path: provisionPath, privileged: true
    end

    # Run a script at every boot
    # config.vm.provision :shell, path: "scripts/vagrantStart.sh", run: "always", privileged: true
    if File.exist? startPath then
        config.vm.provision :shell, path: startPath, run: "always", privileged: true
    end
end # /Configure
