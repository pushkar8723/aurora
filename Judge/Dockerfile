# DOCKER-VERSION 1.12.0
FROM debian:latest
# INSTALL COMPILERS
RUN apt-get update && apt-get install -y \
  openjdk-7-jdk \
  g++ \
  python \
  python3 \
  perl \
  locales \
  php5 \
  ruby \
  rhino \
  fpc \
  mono-complete \
  bc hsbrainfuck \
  psmisc \
  python3-pip
RUN locale-gen "en_US.UTF-8"
COPY requirements.txt /tmp/
RUN pip3 install --requirement /tmp/requirements.txt
COPY judge.py /judge/
RUN mkdir /judge/env
RUN mkdir /judge/io_cache
CMD ["python3", "/judge/judge.py"]