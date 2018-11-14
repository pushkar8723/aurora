# DOCKER-VERSION 1.12.0
FROM ubuntu:latest
ARG DEBIAN_FRONTEND=noninteractive
# INSTALL COMPILERS
RUN apt-get update && apt-get install -y \
  openjdk-8-jdk \
  g++ \
  python \
  python3 \
  perl \
  locales \
  php \
  ruby \
  rhino \
  fpc \
  mono-complete mono-mcs \
  bf bc \
  psmisc \
  python3-pip
RUN locale-gen "en_US.UTF-8"
COPY requirements.txt /tmp/
RUN pip3 install --requirement /tmp/requirements.txt
RUN chmod 700 /tmp
RUN useradd -m -u 8723 -s /bin/bash judge
WORKDIR /home/judge
RUN mkdir env io_cache
RUN chmod 755 env
RUN chmod 700 io_cache
RUN chown judge env
RUN chgrp judge env
COPY judge.py /home/judge/
EXPOSE 8723
CMD ["python3", "judge.py", "-judge", "-cache"]